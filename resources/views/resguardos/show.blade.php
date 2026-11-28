@extends('layouts.app')
@use('Illuminate\Support\Carbon')

@section('eyebrow', 'Resguardos')
@section('title', "Resguardo {$resguardo->folio_resguardo}")

@section('actions')
    <a href="{{ route('resguardos.index') }}" class="btn btn-secondary">← Volver</a>
    <a href="{{ route('resguardos.edit', $resguardo->id_resguardo) }}" class="btn btn-secondary">Editar</a>
    <form method="POST" action="{{ route('resguardos.destroy', $resguardo->id_resguardo) }}"
          onsubmit="return confirm('¿Eliminar el resguardo {{ $resguardo->folio_resguardo }}?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar</button>
    </form>
@endsection

@section('content')
    @php($mueble = $resguardo->mueble)
    @php($usuario = $resguardo->usuario)

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-5">
                <div>
                    <p class="text-sm text-slate-500">Folio</p>
                    <p class="text-2xl font-bold text-institucional">{{ $resguardo->folio_resguardo }}</p>
                </div>
                <span class="badge {{ $resguardo->estado_resguardo->badgeClasses() }} text-sm">{{ $resguardo->estado_resguardo->label() }}</span>
            </div>

            <dl class="mt-5 grid gap-5 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Fecha del resguardo</dt>
                    <dd class="font-semibold">{{ Carbon::parse($resguardo->fech_resguardo)->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Ubicación</dt>
                    <dd class="font-semibold">{{ $resguardo->ubicacion?->nombre_area }}</dd>
                    <dd class="text-slate-500">{{ $resguardo->ubicacion?->edificio?->nombre_edificio }} · Planta {{ $resguardo->ubicacion?->edificio?->planta }}</dd>
                </div>
            </dl>

            <h2 class="mt-8 mb-3 text-sm font-bold tracking-wide text-oscuro uppercase">Responsable</h2>
            <div class="flex items-center gap-4 rounded-lg bg-slate-50 p-4">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-oscuro font-bold text-lima">
                    {{ mb_substr($usuario?->nombre_usuario ?? '?', 0, 1) }}{{ mb_substr($usuario?->apaterno_usuario ?? '', 0, 1) }}
                </span>
                <div class="text-sm">
                    <p class="font-semibold">{{ $usuario?->nombre_completo }}</p>
                    <p class="text-slate-500">{{ $usuario?->correo_usuario }}</p>
                    <p class="text-slate-500">{{ $usuario?->area_adscripcion }}</p>
                </div>
            </div>
        </div>

        <aside class="card h-fit overflow-hidden">
            <div class="bg-institucional px-5 py-3">
                <h2 class="text-sm font-bold tracking-wide text-white uppercase">Mueble resguardado</h2>
            </div>
            <div class="border-b border-slate-100 px-5 py-4">
                <p class="text-lg font-bold">{{ $mueble?->nombre_mueble }}</p>
                <p class="text-xs font-semibold text-dorado">{{ $mueble?->codigo_mueble }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $mueble?->descripcion_mueble }}</p>
            </div>
            <dl class="divide-y divide-slate-100 text-sm">
                @foreach ([
                    'Categoría' => $mueble?->categoria,
                    'Marca' => $mueble?->marca_mueble,
                    'Modelo' => $mueble?->modelo_mueble,
                    'Estado' => $mueble?->estado_mueble,
                    'Área actual' => $mueble?->area?->nombre_area,
                ] as $etiqueta => $valor)
                    <div class="flex justify-between gap-4 px-5 py-2.5">
                        <dt class="text-slate-500">{{ $etiqueta }}</dt>
                        <dd class="text-right font-medium">{{ $valor ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </aside>
    </div>
@endsection
