<?php

namespace Database\Seeders;

use App\Models\ContentTopic;
use App\Models\Post;
use App\Services\Blog\SchemaBuilder;
use Illuminate\Database\Seeder;

/**
 * Vult de blog-machine: de onderwerpen-wachtrij uit de brand-kit plus twee
 * gepubliceerde artikelen, zodat /blog en het blogbeheer er gevuld uitzien.
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // De onderwerpen-wachtrij, zelfde logica als het blog:plan-command
        foreach (config('seo-content.clusters') as $cluster => $topics) {
            foreach ($topics as $prioriteit => $topic) {
                ContentTopic::create([
                    'topic' => $topic,
                    'cluster' => $cluster,
                    'intent' => str_contains(mb_strtolower($topic), 'kost') ? 'commercieel' : 'informationeel',
                    'priority' => min(9, $prioriteit + 2),
                ]);
            }
        }

        $cta = config('seo-content.cta');
        $ctaHtml = '<div class="artikel-cta"><h2>' . e($cta['titel']) . '</h2><p>' . e($cta['tekst']) . '</p><p><a href="' . e($cta['knop_url']) . '">' . e($cta['knop_label']) . '</a></p></div>';

        $artikelen = [
            [
                'title' => 'Een eigen bestelpagina voor je pizzeria: zo werkt het',
                'slug' => 'eigen-bestelpagina-voor-je-pizzeria',
                'keyword' => 'eigen bestelpagina pizzeria',
                'cluster' => 'Online bestellen zonder commissie',
                'meta' => 'Wat levert een eigen bestelpagina je pizzeria op en hoe begin je? Praktische uitleg over online bestellen zonder commissie per bestelling.',
                'dagen' => 12,
                'html' => <<<'HTML'
<p>Elke bestelling die via een bezorgplatform binnenkomt, kost je een deel van je marge. Voor veel pizzeria's voelt dat als een noodzakelijk kwaad: je wilt online vindbaar zijn, dus je betaalt mee.</p>
<p>Toch is er een andere route. Met een eigen bestelpagina houd je de volledige omzet zelf en bouw je aan je eigen klantenbestand in plaats van dat van een platform.</p>
<h2>Wat een eigen bestelpagina je oplevert</h2>
<p>Het grootste verschil zit in de commissie: die is er niet. Klanten betalen rechtstreeks aan jouw zaak, en jij bepaalt zelf hoe je menukaart eruitziet. Via <a href="/onboarding">een gratis proefmaand</a> kun je testen of het bij je zaak past.</p>
<h2>Zo begin je vandaag nog</h2>
<p>Zet je menukaart online, kies een stijl die bij je zaak past en deel de link met je vaste klanten. Een kaartje bij de doos of een sticker op de toonbank doet vaak meer dan je denkt.</p>
<h2>Veelgestelde vragen</h2>
<h3>Wat kost een eigen bestelpagina?</h3>
<p>Bij MijnPizzeria betaal je 24,95 euro per maand, maandelijks opzegbaar. De eerste 30 dagen zijn gratis, zonder betaalgegevens vooraf.</p>
<h3>Heb ik technische kennis nodig?</h3>
<p>Nee. Je stelt je menukaart zelf samen en kiest een ontwerpstijl; alles werkt vanuit een overzichtelijk dashboard.</p>
HTML,
            ],
            [
                'title' => 'Google Bedrijfsprofiel voor je pizzeria: de basis goed neerzetten',
                'slug' => 'google-bedrijfsprofiel-voor-je-pizzeria',
                'keyword' => 'google bedrijfsprofiel pizzeria',
                'cluster' => 'Online zichtbaarheid',
                'meta' => 'Met een goed ingevuld Google Bedrijfsprofiel wordt je pizzeria gevonden door mensen die nu honger hebben. Zo zet je de basis in een avond neer.',
                'dagen' => 5,
                'html' => <<<'HTML'
<p>Wie 's avonds "pizzeria" intikt op zijn telefoon, ziet eerst het kaartje van Google met drie zaken erin. Sta je daar niet tussen, dan bestelt die klant bij de buurman.</p>
<p>Het goede nieuws: je Google Bedrijfsprofiel goed neerzetten kost je één rustige avond en het effect houdt maanden aan.</p>
<h2>Claim je profiel en vul alles in</h2>
<p>Zoek je zaak op Google Maps en claim het profiel via de knop bij je bedrijfsnaam. Vul daarna alles in: openingstijden, telefoonnummer, foto's van je zaak en je pizza's, en een korte omschrijving. Profielen die compleet zijn ingevuld, worden aantoonbaar vaker getoond.</p>
<h2>Zet je bestellink op de juiste plek</h2>
<p>Google laat je een bestellink toevoegen aan je profiel. Zet daar de link naar je eigen bestelpagina neer, niet die van een bezorgplatform. Zo landt een klant die jou al gevonden heeft direct bij jou, zonder commissie onderweg.</p>
<h2>Foto's doen het zware werk</h2>
<p>Mensen kiezen met hun ogen. Drie goede foto's van je bekendste pizza's, gemaakt bij daglicht bij het raam, doen meer dan tien donkere telefoonfoto's. Ververs ze elk seizoen, dan blijft je profiel actueel.</p>
<h2>Veelgestelde vragen</h2>
<h3>Kost een Google Bedrijfsprofiel geld?</h3>
<p>Nee, een Bedrijfsprofiel is gratis. Je hebt alleen een Google-account nodig en een manier om te bevestigen dat de zaak van jou is.</p>
<h3>Hoe vaak moet ik mijn profiel bijwerken?</h3>
<p>Controleer minimaal elk kwartaal je openingstijden en voeg af en toe een nieuwe foto toe. Rond feestdagen loont het om je aangepaste tijden direct in te voeren.</p>
HTML,
            ],
        ];

        foreach ($artikelen as $a) {
            $coverPad = '/blog-covers/' . $a['slug'] . '.jpg';
            $post = Post::create([
                'title' => $a['title'],
                'slug' => $a['slug'],
                'content' => $a['html'] . "\n" . $ctaHtml,
                'excerpt' => \Illuminate\Support\Str::limit(trim(strip_tags(\Illuminate\Support\Str::before($a['html'], '<h2'))), 180),
                'meta_title' => \Illuminate\Support\Str::limit($a['title'], 58, ''),
                'meta_description' => $a['meta'],
                'og_title' => $a['title'],
                'og_description' => $a['meta'],
                'target_keyword' => $a['keyword'],
                'cluster' => $a['cluster'],
                'cover' => file_exists(public_path($coverPad)) ? $coverPad : null,
                'generation_status' => 'published',
                'published_at' => now()->subDays($a['dagen']),
                'briefing' => ['keyword' => $a['keyword'], 'cluster' => $a['cluster'], 'cover_credit' => null, 'cover_bron' => null],
                'validator_metrics' => ['woorden' => str_word_count(strip_tags($a['html'])), 'h2_count' => substr_count($a['html'], '<h2'), 'kapotte_links' => [], 'keyword_in_tekst' => true, 'issues' => []],
                'ai_review' => ['verdict' => 'pass', 'reasons' => ['Prijzen en features komen overeen met de factsheet.']],
            ]);
            $post->update(['schema_json' => app(SchemaBuilder::class)->voorPost($post->refresh())]);

            ContentTopic::where('topic', 'like', '%' . mb_substr($a['keyword'], 0, 20) . '%')->update(['status' => 'published', 'post_id' => $post->id]);
        }
    }
}
