@extends('layouts.app')

@section('eyebrow', 'Movimientos')
@section('title', "Editar movimiento #{$movimiento->id_mov}")

@section('content')
    <form method="POST" action="{{ route('movimientos.update', $movimiento->id_mov) }}">
        @csrf
        @method('PUT')
        @include('movimientos._form')
    </form>
@endsection
