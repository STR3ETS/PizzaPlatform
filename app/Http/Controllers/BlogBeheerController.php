<?php

namespace App\Http\Controllers;

use App\Models\ContentTopic;
use App\Models\Post;
use App\Services\Blog\AutonomousDrafter;
use App\Services\Blog\ContentReviewer;
use App\Services\Blog\LlmClient;
use App\Services\Blog\SchemaBuilder;
use App\Services\Blog\SemanticMeshService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Throwable;

/**
 * De blog-machine in het beheerdashboard: onderwerpen plannen, concepten
 * laten schrijven, de AI-review-gate en het publiceren zelf.
 */
class BlogBeheerController extends Controller
{
    private function eis(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /** Nieuw onderwerp handmatig in de wachtrij zetten */
    public function topicToevoegen(Request $request): RedirectResponse
    {
        $this->eis($request);

        $data = $request->validate([
            'topic' => ['required', 'string', 'max:200'],
            'cluster' => ['required', 'string', 'max:120'],
            'priority' => ['required', 'integer', 'min:1', 'max:9'],
        ]);
        ContentTopic::create($data + ['intent' => 'informationeel']);

        return redirect('/admin/blog')->with('blogSucces', 'Onderwerp toegevoegd aan de wachtrij.');
    }

    public function topicVerwijderen(Request $request, ContentTopic $topic): RedirectResponse
    {
        $this->eis($request);
        abort_if($topic->status === 'drafting', 409, 'Dit onderwerp wordt nu geschreven.');
        $topic->delete();

        return redirect('/admin/blog')->with('blogSucces', 'Onderwerp verwijderd.');
    }

    /** De seed-onderwerpen uit de brand-kit in de wachtrij zetten */
    public function plan(Request $request): RedirectResponse
    {
        $this->eis($request);
        Artisan::call('blog:plan');

        return redirect('/admin/blog')->with('blogSucces', trim(Artisan::output()));
    }

    /** Eén concept laten schrijven door de content-machine (duurt even) */
    public function schrijf(Request $request, AutonomousDrafter $drafter, LlmClient $llm): RedirectResponse
    {
        $this->eis($request);

        if (! $llm->isGeconfigureerd()) {
            return redirect('/admin/blog')->with('blogFout', 'Geen LLM-key geconfigureerd (SEO_LLM_KEY in .env).');
        }

        @set_time_limit(600);
        try {
            $resultaat = $drafter->draftVolgende();
        } catch (Throwable $e) {
            return redirect('/admin/blog')->with('blogFout', 'Schrijven mislukt: ' . $e->getMessage());
        }

        return redirect('/admin/blog')->with('blogSucces', $resultaat['melding']);
    }

    /** Voorbeeld van een concept: de echte blogpagina met een beheer-balk erboven */
    public function voorbeeld(Request $request, Post $post): View
    {
        $this->eis($request);

        $verwant = Post::published()->whereKeyNot($post->id)->where('cluster', $post->cluster)->limit(3)->get();

        return view('site.blog.show', ['post' => $post, 'verwant' => $verwant, 'preview' => true]);
    }

    /** De AI-review-gate opnieuw draaien voor een concept */
    public function hergate(Request $request, Post $post, ContentReviewer $reviewer): RedirectResponse
    {
        $this->eis($request);

        @set_time_limit(300);
        try {
            $post->update(['ai_review' => $reviewer->review($post)]);
        } catch (Throwable $e) {
            return redirect('/admin/blog')->with('blogFout', 'Review-gate mislukt: ' . $e->getMessage());
        }

        return redirect('/admin/blog')->with('blogSucces', 'Review-gate opnieuw gedraaid voor "' . $post->title . '".');
    }

    /** Publiceren kan alleen langs de gate, of bewust eroverheen met "forceer" */
    public function publiceer(Request $request, Post $post, SemanticMeshService $mesh, SchemaBuilder $schema): RedirectResponse
    {
        $this->eis($request);

        $verdict = $post->ai_review['verdict'] ?? null;
        if ($verdict !== 'pass' && ! $request->boolean('forceer')) {
            return redirect('/admin/blog')->with('blogFout', 'De review-gate staat op "' . ($verdict ?? 'nog niet beoordeeld') . '". Los de redenen op of vink "toch publiceren" aan om de gate bewust te overrulen.');
        }

        $post->update([
            'generation_status' => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        /* Publicatie-flow: interne links weven + schema genereren */
        $mesh->weave($post);
        $post->update(['schema_json' => $schema->voorPost($post->refresh())]);

        ContentTopic::where('post_id', $post->id)->update(['status' => 'published']);

        return redirect('/admin/blog')->with('blogSucces', 'Gepubliceerd: /blog/' . $post->slug . ' (interne links geweven, schema gegenereerd).');
    }

    /** Concept afwijzen: post weg, onderwerp terug in de wachtrij */
    public function afwijzen(Request $request, Post $post): RedirectResponse
    {
        $this->eis($request);

        ContentTopic::where('post_id', $post->id)->update(['status' => 'planned', 'post_id' => null]);
        $post->delete();

        return redirect('/admin/blog')->with('blogSucces', 'Concept afgewezen; het onderwerp staat weer in de wachtrij.');
    }

    /** Gepubliceerd artikel offline halen (terug naar de review-wachtrij) */
    public function offline(Request $request, Post $post): RedirectResponse
    {
        $this->eis($request);

        $post->update(['generation_status' => 'needs_review']);
        ContentTopic::where('post_id', $post->id)->update(['status' => 'drafted']);

        return redirect('/admin/blog')->with('blogSucces', '"' . $post->title . '" staat offline en terug in de review-wachtrij.');
    }
}
