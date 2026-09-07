@extends('feedback.layout')

@section('titel', 'Twee weken extra, van ons')
@section('icoon', 'gift')

@section('inhoud')
    <p class="text-[11px] font-semibold uppercase tracking-wider text-basil mb-1">Een extra kans</p>
    <p class="text-sm font-semibold text-cacao/55 mb-4">
        Je proefperiode voor <b>{{ $zaak->onboarding['name'] ?? 'jouw pizzeria' }}</b> is verlopen, maar alles staat er nog:
        je bestelpagina, je menukaart en je instellingen. Met één klik krijg je twee weken extra om het rustig af te maken.
        Gratis, zonder voorwaarden.
    </p>
    <form method="POST" action="{{ \Illuminate\Support\Facades\URL::signedRoute('feedback.verleng', ['user' => $zaak->id]) }}">
        @csrf
        <button type="submit" class="btn-primary w-full !py-3.5 !text-lg">Ja, geef mij twee weken extra</button>
    </form>
    <p class="mt-4 text-xs font-semibold text-cacao/40">Liever niet? Dan hoef je niks te doen; dit was onze laatste mail hierover.</p>
@endsection
