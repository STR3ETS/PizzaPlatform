@extends('bestel.layout')

@section('inhoud')
    {{-- Elk template is een eigen ontwerp met eigen secties, niet alleen andere kleuren --}}
    @include('bestel.templates.' . $themaId)
@endsection
