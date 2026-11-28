@extends('layouts.app')

@section('eyebrow', 'Movimientos')
@section('title', 'Nuevo movimiento')

@section('content')
    <form method="POST" action="{{ route('movimientos.store') }}">
        @csrf
        @include('movimientos._form')
    </form>
@endsection
