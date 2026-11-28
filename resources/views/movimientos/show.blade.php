@extends('layouts.app')
@use('App\Enums\EstadoMovimiento')
@use('Illuminate\Support\Carbon')

@section('eyebrow', 'Movimientos')
@section('title', "Movimiento #{$movimiento->id_mov}")

@section('actions')
    <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">← Volver</a>
    @unless ($movimiento->estado_mov->esFinal())
        <a href="{{ route('movimientos.edit', $movimiento->id_mov) }}" class="btn btn-secondary">Editar</a>
        <form method="POST" action="{{ route('movimientos.destroy', $movimiento->id_mov) }}"
              onsubmit="return confirm('¿Eliminar este movimiento?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
    @endunless
@endsection

@section('content')
    @php($mueble = $movimiento->mueble)

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Trayecto del mueble --}}
            <div class="card p-6">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-bold">Trayecto</h2>
                    <span class="badge {{ $movimiento->estado_mov->badgeClasses() }} text-sm">{{ $movimiento->estado_mov->label() }}</span>
                </div>

                <div class="grid items-center gap-4 sm:grid-cols-[1fr_auto_1fr]">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold tracking-wide text-slate-500 uppercase">Origen</p>
                        <p class="mt-1 text-lg font-bold">{{ $movimiento->area_origen?->nombre_area }}</p>
                        <p class="text-sm text-slate-500">{{ $movimiento->area_origen?->edificio?->nombre_edificio }}</p>
                    </div>
                    <div class="text-center text-3xl font-bold text-dorado">→</div>
                    <div class="rounded-lg border-2 border-institucional bg-institucional/5 p-4">
                        <p class="text-xs font-bold tracking-wide text-institucional uppercase">Destino</p>
                        <p class="mt-1 text-lg font-bold">{{ $movimiento->area_destino?->nombre_area }}</p>
                        <p class="text-sm text-slate-500">
                            {{ $movimiento->area_destino?->edificio?->nombre_edificio }} · Planta {{ $movimiento->area_destino?->edificio?->planta }}
                        </p>
                    </div>
                </div>

                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-slate-500">Tipo</dt>
                        <dd class="font-semibold">{{ $movimiento->tipo_mov->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Fecha del movimiento</dt>
                        <dd class="font-semibold">{{ Carbon::parse($movimiento->fech_mov)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Fecha de validación</dt>
                        <dd class="font-semibold">{{ $movimiento->fechval_mov ? Carbon::parse($movimiento->fechval_mov)->format('d/m/Y') : 'Pendiente' }}</dd>
                    </div>
                </dl>

                @if ($movimiento->observaciones_mov)
                    <div class="mt-6">
                        <p class="text-sm text-slate-500">Observaciones</p>
                        <p class="mt-1 rounded-lg bg-slate-50 p-3 text-sm whitespace-pre-line">{{ $movimiento->observaciones_mov }}</p>
                    </div>
                @endif
            </div>

            {{-- Validación del movimiento --}}
            @if ($movimiento->estado_mov->esFinal())
                <div class="card p-6 text-sm text-slate-600">
                    Este movimiento se cerró como <strong>{{ $movimiento->estado_mov->label() }}</strong>
                    el {{ Carbon::parse($movimiento->fechval_mov)->format('d/m/Y') }}. Se conserva como historial.
                </div>
            @else
                <form method="POST" action="{{ route('movimientos.estado', $movimiento->id_mov) }}" class="card p-6">
                    @csrf
                    @method('PATCH')

                    <h2 class="text-lg font-bold">Validar movimiento</h2>
                    <p class="mt-1 text-sm text-slate-500">Elige cómo cerrar el movimiento. Esta acción no se puede deshacer.</p>

                    <label for="observaciones" class="form-label mt-4">Comentario de validación (opcional)</label>
                    <textarea id="observaciones" name="observaciones" rows="2" class="form-input"
                              placeholder="Ej. Recibido en buen estado por el área destino">{{ old('observaciones') }}</textarea>
                    @error('observaciones') <p class="form-error">{{ $message }}</p> @enderror
                    @error('estado_mov') <p class="form-error">{{ $message }}</p> @enderror

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <button type="submit" name="estado_mov" value="{{ EstadoMovimiento::Completada->value }}"
                                class="btn bg-lima text-oscuro hover:bg-lima/80 focus-visible:ring-lima">
                            ✓ Completar
                        </button>
                        <button type="submit" name="estado_mov" value="{{ EstadoMovimiento::Rechazada->value }}"
                                class="btn btn-danger">
                            ✕ Rechazar
                        </button>
                        <button type="submit" name="estado_mov" value="{{ EstadoMovimiento::Baja->value }}"
                                class="btn bg-vino text-white hover:bg-oscuro focus-visible:ring-vino"
                                onclick="return confirm('El mueble quedará dado de baja. ¿Continuar?')">
                            Dar de baja
                        </button>
                    </div>
                    <ul class="mt-4 space-y-1 text-xs text-slate-500">
                        <li><strong class="text-oscuro">Completar:</strong> el mueble pasa al área destino.</li>
                        <li><strong class="text-guinda">Rechazar:</strong> el mueble se queda en su área actual.</li>
                        <li><strong class="text-vino">Dar de baja:</strong> el mueble queda fuera de servicio.</li>
                    </ul>
                </form>
            @endif
        </div>

        {{-- Información del mueble --}}
        <aside class="card h-fit overflow-hidden">
            <div class="bg-institucional px-5 py-3">
                <h2 class="text-sm font-bold tracking-wide text-white uppercase">Mueble</h2>
            </div>
            <div class="border-b border-slate-100 px-5 py-4">
                <p class="text-lg font-bold">{{ $mueble->nombre_mueble }}</p>
                <p class="text-xs font-semibold text-dorado">{{ $mueble->codigo_mueble }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $mueble->descripcion_mueble }}</p>
            </div>
            <dl class="divide-y divide-slate-100 text-sm">
                @foreach ([
                    'Categoría' => $mueble->categoria,
                    'Marca' => $mueble->marca_mueble,
                    'Modelo' => $mueble->modelo_mueble,
                    'Estado' => $mueble->estado_mueble,
                    'Área actual' => $mueble->area?->nombre_area,
                    'Ubicación' => $mueble->descri_ubicacion,
                    'Responsable' => $mueble->usuario?->nombre_completo,
                    'Fecha de registro' => Carbon::parse($mueble->fechreg_mueble)->format('d/m/Y'),
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
