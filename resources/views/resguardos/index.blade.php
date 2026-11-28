@extends('layouts.app')
@use('App\Enums\EstadoResguardo')
@use('Illuminate\Support\Carbon')

@section('eyebrow', 'Control de bienes')
@section('title', 'Resguardos')

@section('actions')
    <a href="{{ route('resguardos.create') }}" class="btn btn-primary">+ Nuevo resguardo</a>
@endsection

@section('content')
    <div class="card">
        @include('partials.filtros', [
            'ruta' => route('resguardos.index'),
            'placeholder' => 'Folio, mueble, código o responsable',
            'estados' => EstadoResguardo::cases(),
            'estadoActual' => $estadoActual,
        ])

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Folio</th>
                        <th class="px-4 py-3">Mueble</th>
                        <th class="px-4 py-3">Responsable</th>
                        <th class="px-4 py-3">Ubicación</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($resguardos as $resguardo)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold whitespace-nowrap text-institucional">{{ $resguardo->folio_resguardo }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold">{{ $resguardo->mueble?->nombre_mueble }}</p>
                                <p class="text-xs text-slate-500">{{ $resguardo->mueble?->codigo_mueble }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $resguardo->usuario?->nombre_completo }}</td>
                            <td class="px-4 py-3">{{ $resguardo->ubicacion?->nombre_area }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ Carbon::parse($resguardo->fech_resguardo)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $resguardo->estado_resguardo->badgeClasses() }}">{{ $resguardo->estado_resguardo->label() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <x-boton-accion icono="ver" :href="route('resguardos.show', $resguardo->id_resguardo)" />
                                    <x-boton-accion icono="editar" :href="route('resguardos.edit', $resguardo->id_resguardo)" />
                                    <x-boton-accion icono="eliminar" :action="route('resguardos.destroy', $resguardo->id_resguardo)"
                                                    :confirmar="'¿Eliminar el resguardo '.$resguardo->folio_resguardo.'?'" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                No hay resguardos que coincidan con la búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
