@extends('feedback.layout')

@section('titel', $conf['titel'])
@section('icoon', 'comment-dots')

@section('inhoud')
    <p class="text-[11px] font-semibold uppercase tracking-wider text-basil mb-1">{{ $conf['kicker'] }}</p>
    <p class="text-sm font-semibold text-cacao/55 mb-5">{{ $conf['sub'] }}</p>

    <form method="POST" action="{{ \Illuminate\Support\Facades\URL::signedRoute('feedback.opslaan', ['user' => $zaak->id, 'context' => $context]) }}" class="space-y-2.5">
        @csrf
        @foreach($conf['opties'] as $optie)
            <label class="choice-card !py-3.5 cursor-pointer flex items-center gap-3">
                <input type="radio" name="antwoord" value="{{ $optie }}" class="w-4 h-4 shrink-0" style="accent-color:var(--color-tomato)">
                <span class="flex-1 text-left text-sm font-semibold">{{ $optie }}</span>
            </label>
        @endforeach
        <div class="pt-2">
            <label for="toelichting" class="lbl">{{ $conf['toelichting'] }}</label>
            <textarea id="toelichting" name="toelichting" rows="3" class="inp !text-sm w-full" placeholder="Typ hier, mag ook leeg blijven"></textarea>
        </div>
        <button type="submit" class="btn-primary w-full !py-3 mt-2">Versturen</button>
    </form>
@endsection
