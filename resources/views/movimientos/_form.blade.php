{{--
    Formulario compartido para crear y editar movimientos.
    Variables: $muebles, $areas, $tipos y opcionalmente $movimiento.
--}}
@php
    $movimiento ??= null;
    $muebleSeleccionado = old('id_mueble', $movimiento?->id_mueble);
    $destinoSeleccionado = old('id_area_destino', $movimiento?->id_area_destino);

    $datosMuebles = $muebles->map(fn ($mueble) => [
        'nombre' => $mueble->nombre_mueble,
        'codigo' => $mueble->codigo_mueble,
        'descripcion' => $mueble->descripcion_mueble,
        'categoria' => $mueble->categoria,
        'marca' => $mueble->marca_mueble,
        'modelo' => $mueble->modelo_mueble,
        'estado' => $mueble->estado_mueble,
        'ubicacion' => $mueble->descri_ubicacion,
        'id_area' => $mueble->id_area,
        'area' => $mueble->area?->nombre_area,
        'edificio' => $mueble->area?->edificio?->nombre_edificio,
        'responsable' => $mueble->usuario?->nombre_completo,
    ]);
@endphp

<div class="grid gap-6 lg:grid-cols-5">
    <div class="card space-y-5 p-6 lg:col-span-3">
        <div>
            <label for="id_mueble" class="form-label">Mueble a mover</label>
            <select id="id_mueble" name="id_mueble" class="form-input" required>
                <option value="">Selecciona un mueble…</option>
                @foreach ($muebles as $mueble)
                    <option value="{{ $mueble->id_mueble }}"
                            @selected($muebleSeleccionado == $mueble->id_mueble)
                            @disabled($mueble->estado_mueble === 'Baja')>
                        {{ $mueble->codigo_mueble }} — {{ $mueble->nombre_mueble }}{{ $mueble->estado_mueble === 'Baja' ? ' (dado de baja)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('id_mueble') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="form-label">Área de origen</label>
                <p id="area-origen" class="form-input bg-slate-50 text-slate-500">Selecciona un mueble</p>
                <p class="mt-1 text-xs text-slate-500">Se toma del área actual del mueble.</p>
            </div>
            <div>
                <label for="id_area_destino" class="form-label">Área destino</label>
                <select id="id_area_destino" name="id_area_destino" class="form-input" required>
                    <option value="">Selecciona un área…</option>
                    @foreach ($areas->groupBy(fn ($area) => $area->edificio->nombre_edificio) as $edificio => $areasEdificio)
                        <optgroup label="{{ $edificio }}">
                            @foreach ($areasEdificio as $area)
                                <option value="{{ $area->id_area }}" @selected($destinoSeleccionado == $area->id_area)>
                                    {{ $area->nombre_area }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('id_area_destino') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="tipo_mov" class="form-label">Tipo de movimiento</label>
                <select id="tipo_mov" name="tipo_mov" class="form-input" required>
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo->value }}" @selected(old('tipo_mov', $movimiento?->tipo_mov?->value) === $tipo->value)>
                            {{ $tipo->label() }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_mov') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="fech_mov" class="form-label">Fecha del movimiento</label>
                <input type="date" id="fech_mov" name="fech_mov" class="form-input" required
                       value="{{ old('fech_mov', $movimiento?->fech_mov ?? now()->toDateString()) }}">
                @error('fech_mov') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="observaciones_mov" class="form-label">Observaciones</label>
            <textarea id="observaciones_mov" name="observaciones_mov" rows="4" class="form-input"
                      placeholder="Motivo del movimiento, condiciones del mueble, etc.">{{ old('observaciones_mov', $movimiento?->observaciones_mov) }}</textarea>
            @error('observaciones_mov') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        @unless ($movimiento)
            <p class="rounded-lg bg-institucional/10 px-4 py-3 text-sm text-institucional">
                El movimiento se registra <strong>En curso</strong>. Después podrás marcarlo como Completado, Rechazado o Baja.
            </p>
        @endunless
    </div>

    {{-- Información del mueble seleccionado --}}
    <aside class="lg:col-span-2">
        <div class="card overflow-hidden lg:sticky lg:top-6">
            <div class="bg-institucional px-5 py-3">
                <h2 class="text-sm font-bold tracking-wide text-white uppercase">Información del mueble</h2>
            </div>
            <div id="info-mueble-vacio" class="p-6 text-center text-sm text-slate-500">
                Selecciona un mueble para ver su información.
            </div>
            <dl id="info-mueble" class="hidden divide-y divide-slate-100 text-sm">
                <div class="px-5 py-4">
                    <dd data-campo="nombre" class="text-lg font-bold text-oscuro"></dd>
                    <dd data-campo="codigo" class="text-xs font-semibold text-dorado"></dd>
                    <dd data-campo="descripcion" class="mt-1 text-slate-600"></dd>
                </div>
                @foreach (['categoria' => 'Categoría', 'marca' => 'Marca', 'modelo' => 'Modelo', 'estado' => 'Estado', 'area' => 'Área actual', 'edificio' => 'Edificio', 'ubicacion' => 'Ubicación', 'responsable' => 'Responsable'] as $campo => $etiqueta)
                    <div class="flex justify-between gap-4 px-5 py-2.5">
                        <dt class="text-slate-500">{{ $etiqueta }}</dt>
                        <dd data-campo="{{ $campo }}" class="text-right font-medium"></dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </aside>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ $movimiento ? route('movimientos.show', $movimiento->id_mov) : route('movimientos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">{{ $movimiento ? 'Guardar cambios' : 'Registrar movimiento' }}</button>
</div>

@push('scripts')
    <script>
        (() => {
            const muebles = @json($datosMuebles);
            const selectMueble = document.getElementById('id_mueble');
            const selectDestino = document.getElementById('id_area_destino');
            const origen = document.getElementById('area-origen');
            const info = document.getElementById('info-mueble');
            const vacio = document.getElementById('info-mueble-vacio');

            function actualizar() {
                const mueble = muebles[selectMueble.value];

                info.classList.toggle('hidden', !mueble);
                vacio.classList.toggle('hidden', !!mueble);
                origen.textContent = mueble ? `${mueble.area} (${mueble.edificio})` : 'Selecciona un mueble';

                // El área actual del mueble no puede ser el destino.
                for (const opcion of selectDestino.options) {
                    const esOrigen = mueble && Number(opcion.value) === mueble.id_area;
                    opcion.disabled = esOrigen;
                    if (esOrigen && opcion.selected) selectDestino.value = '';
                }

                if (!mueble) return;
                for (const campo of info.querySelectorAll('[data-campo]')) {
                    campo.textContent = mueble[campo.dataset.campo] ?? '—';
                }
            }

            selectMueble.addEventListener('change', actualizar);
            actualizar();
        })();
    </script>
@endpush
