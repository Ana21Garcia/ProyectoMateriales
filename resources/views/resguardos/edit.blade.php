@extends('layouts.app')

@section('eyebrow', 'Resguardos')
@section('title', "Editar resguardo {$resguardo->folio_resguardo}")

@section('content')
    <form method="POST" action="{{ route('resguardos.update', $resguardo->id_resguardo) }}" class="max-w-3xl">
        @csrf
        @method('PUT')
        @include('resguardos._form')
    </form>
@endsection
