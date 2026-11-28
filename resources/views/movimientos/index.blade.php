@extends('layouts.app')
@use('App\Enums\EstadoMovimiento')

@section('eyebrow', 'Control de bienes')
@section('title', 'Movimientos')

@section('actions')
    <a href="{{ route('movimientos.create') }}" class="btn btn-primary">+ Nuevo movimiento</a>
@endsection

@section('content')
    <div class="card">
        @include('partials.filtros', [
            'ruta' => route('movimientos.index'),
            'placeholder' => 'Mueble, código, categoría, marca o área',
            'estados' => EstadoMovimiento::cases(),
            'estadoActual' => $estadoActual,
        ])

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Mueble</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Origen → Destino</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($movimientos as $movimiento)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-500">{{ $movimiento->id_mov }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold">{{ $movimiento->mueble->nombre_mueble }}</p>
                                <p class="text-xs text-slate-500">{{ $movimiento->mueble->codigo_mueble }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $movimiento->tipo_mov->label() }}</td>
                            <td class="px-4 py-3">
                                <span class="text-slate-500">{{ $movimiento->area_origen?->nombre_area }}</span>
                                <span class="mx-1 text-dorado">→</span>
                                <span class="font-semibold">{{ $movimiento->area_destino?->nombre_area }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($movimiento->fech_mov)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $movimiento->estado_mov->badgeClasses() }}">{{ $movimiento->estado_mov->label() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <x-boton-accion icono="ver" :href="route('movimientos.show', $movimiento->id_mov)" />
                                    @unless ($movimiento->estado_mov->esFinal())
                                        <x-boton-accion icono="editar" :href="route('movimientos.edit', $movimiento->id_mov)" />
                                        <x-boton-accion icono="eliminar" :action="route('movimientos.destroy', $movimiento->id_mov)"
                                                        confirmar="¿Eliminar este movimiento?" />
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                No hay movimientos que coincidan con la búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
