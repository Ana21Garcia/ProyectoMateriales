@extends('layouts.app')

@section('eyebrow', 'Resguardos')
@section('title', 'Nuevo resguardo')

@section('content')
    <form method="POST" action="{{ route('resguardos.store') }}" class="max-w-3xl">
        @csrf
        @include('resguardos._form')
    </form>
@endsection
