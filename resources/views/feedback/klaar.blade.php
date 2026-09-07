@extends('feedback.layout')

@section('titel', $titel)
@section('icoon', 'check')

@section('inhoud')
    <p class="text-sm font-semibold text-cacao/55">{{ $tekst }}</p>
    @isset($knop)
        <a href="{{ $knop['url'] }}" class="btn-primary inline-block mt-5 !px-8 !py-3">{{ $knop['label'] }}</a>
    @endisset
@endsection
