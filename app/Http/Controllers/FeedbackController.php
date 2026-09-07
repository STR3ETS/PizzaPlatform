<?php

namespace App\Http\Controllers;

use App\Mail\NieuweFeedbackMail;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * De feedbackformulieren waar de mails naartoe linken: blanco pagina's in de
 * huisstijl met een paar gerichte vragen. De links zijn ondertekend, dus de
 * zaak hoeft niet in te loggen en niemand kan andermans link vervalsen.
 */
class FeedbackController extends Controller
{
    public const CONTEXTEN = [
        'winback' => [
            'kicker' => 'We missen je',
            'titel' => 'Wat hield je tegen?',
            'sub' => 'Eerlijk antwoord helpt ons het beste. Het kost je een halve minuut.',
            'mascotte' => 'sprinkling-cheese',
            'bubbel' => 'Alles wat je vertelt, gebruiken we om het beter te maken.',
            'opties' => ['Te duur voor mijn zaak', 'Ik miste functies', 'Nog geen tijd gehad om het in te richten', 'Ik gebruik al iets anders', 'Iets anders'],
            'toelichting' => 'Wil je er iets bij vertellen? Wat miste er bijvoorbeeld?',
        ],
        'proef' => [
            'kicker' => 'Jouw proefperiode',
            'titel' => 'Waar twijfel je nog over?',
            'sub' => 'Vertel het ons voordat je beslist; grote kans dat we het kunnen oplossen.',
            'mascotte' => 'holding-3-pizzas',
            'bubbel' => 'Zeg het eerlijk, daar helpen we je het snelst mee.',
            'opties' => ['De prijs', 'Of mijn klanten het gaan gebruiken', 'Het instellen kost me te veel tijd', 'Ik mis een functie', 'Iets anders'],
            'toelichting' => 'Vertel er gerust wat meer over.',
        ],
        'hulp' => [
            'kicker' => 'We helpen je graag',
            'titel' => 'Waar kunnen we je mee helpen?',
            'sub' => 'Stel je vraag of vertel waar je vastloopt; we reageren dezelfde werkdag.',
            'mascotte' => 'waving-hello',
            'bubbel' => 'Geen vraag is te klein, daar zijn we voor!',
            'opties' => ['Ik heb een vraag', 'Ik loop ergens vast', 'Ik heb een idee voor het platform', 'Ik wil een compliment kwijt'],
            'toelichting' => 'Vertel het hier.',
        ],
    ];

    public function formulier(Request $request, User $user, string $context)
    {
        abort_unless(isset(self::CONTEXTEN[$context]), 404);

        return view('feedback.formulier', [
            'zaak' => $user,
            'context' => $context,
            'conf' => self::CONTEXTEN[$context],
        ]);
    }

    public function opslaan(Request $request, User $user, string $context)
    {
        abort_unless(isset(self::CONTEXTEN[$context]), 404);
        $data = $request->validate([
            'antwoord' => ['nullable', 'string', 'max:120'],
            'toelichting' => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'context' => $context,
            'antwoord' => $data['antwoord'] ?? null,
            'toelichting' => ($data['toelichting'] ?? '') !== '' ? $data['toelichting'] : null,
        ]);

        // Het beheer hoort het direct: dit zijn de gesprekken die klanten redden
        try {
            if (config('mail.beheer')) {
                Mail::to(config('mail.beheer'))->send(new NieuweFeedbackMail($user, $feedback));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('feedback.klaar', [
            'titel' => 'Dankjewel!',
            'tekst' => 'We hebben je antwoord ontvangen en reageren snel als dat nodig is. Fijne dag!',
        ]);
    }

    /** De zelfbedienings-verlenging uit de winback-mail: twee weken extra proef */
    public function verlenging(Request $request, User $user)
    {
        if ($user->abonnement_actief) {
            return view('feedback.klaar', [
                'titel' => 'Je abonnement loopt al',
                'tekst' => 'Je hoeft niks te verlengen: je abonnement is actief en alles staat voor je open.',
            ]);
        }
        if ($user->inProefperiode()) {
            return view('feedback.klaar', [
                'titel' => 'Je proefperiode loopt nog',
                'tekst' => 'Je kunt nog proberen tot ' . $user->proef_tot->locale('nl')->isoFormat('D MMMM') . '. Veel plezier!',
            ]);
        }

        return view('feedback.verlenging', ['zaak' => $user]);
    }

    public function verleng(Request $request, User $user)
    {
        // Alleen verlengen als de proef echt verlopen is en er geen abonnement loopt
        if (! $user->abonnement_actief && ! $user->inProefperiode()) {
            $user->forceFill(['proef_tot' => now()->addDays(14)])->save();
            \App\Models\MailLog::where('user_id', $user->id)
                ->whereIn('soort', ['proef-bijna-af', 'proef-laatste-dag', 'winback-1', 'winback-2'])
                ->delete();

            try {
                if (config('mail.beheer')) {
                    Mail::to(config('mail.beheer'))->send(new NieuweFeedbackMail($user, new Feedback([
                        'context' => 'verlenging',
                        'antwoord' => 'Heeft zelf twee weken extra proeftijd geclaimd',
                    ])));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('feedback.klaar', [
            'titel' => 'Je hebt er twee weken bij!',
            'tekst' => 'Je proefperiode loopt nu tot ' . $user->fresh()->proef_tot->locale('nl')->isoFormat('D MMMM') . '. Log in en ga verder waar je was gebleven.',
            'knop' => ['url' => route('login'), 'label' => 'Inloggen'],
        ]);
    }
}
