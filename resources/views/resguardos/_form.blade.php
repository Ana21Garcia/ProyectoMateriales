{{--
    Formulario compartido para crear y editar resguardos.
    Variables: $muebles, $usuarios, $areas, $estados, y $folio o $resguardo.
--}}
@php($resguardo ??= null)

<div class="card space-y-5 p-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="form-label">Folio</label>
            <p class="form-input bg-slate-50 font-semibold text-institucional">{{ $resguardo?->folio_resguardo ?? $folio }}</p>
            @unless ($resguardo)
                <p class="mt-1 text-xs text-slate-500">Se asigna automáticamente al guardar.</p>
            @endunless
        </div>
        <div>
            <label for="fech_resguardo" class="form-label">Fecha del resguardo</label>
            <input type="date" id="fech_resguardo" name="fech_resguardo" class="form-input" required
                   value="{{ old('fech_resguardo', $resguardo?->fech_resguardo ?? now()->toDateString()) }}">
            @error('fech_resguardo') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="id_mueble" class="form-label">Mueble</label>
        <select id="id_mueble" name="id_mueble" class="form-input" required>
            <option value="">Selecciona un mueble…</option>
            @foreach ($muebles as $mueble)
                <option value="{{ $mueble->id_mueble }}" data-area="{{ $mueble->id_area }}"
                        @selected(old('id_mueble', $resguardo?->id_mueble) == $mueble->id_mueble)>
                    {{ $mueble->codigo_mueble }} — {{ $mueble->nombre_mueble }} ({{ $mueble->area?->nombre_area }})
                </option>
            @endforeach
        </select>
        @error('id_mueble') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="id_usuario" class="form-label">Responsable del resguardo</label>
            <select id="id_usuario" name="id_usuario" class="form-input" required>
                <option value="">Selecciona un responsable…</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id_usuario }}" @selected(old('id_usuario', $resguardo?->id_usuario) == $usuario->id_usuario)>
                        {{ $usuario->nombre_completo }} — {{ $usuario->area_adscripcion }}
                    </option>
                @endforeach
            </select>
            @error('id_usuario') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="id_ubi" class="form-label">Ubicación</label>
            <select id="id_ubi" name="id_ubi" class="form-input" required>
                <option value="">Selecciona un área…</option>
                @foreach ($areas->groupBy(fn ($area) => $area->edificio->nombre_edificio) as $edificio => $areasEdificio)
                    <optgroup label="{{ $edificio }}">
                        @foreach ($areasEdificio as $area)
                            <option value="{{ $area->id_area }}" @selected(old('id_ubi', $resguardo?->id_ubi) == $area->id_area)>
                                {{ $area->nombre_area }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Al elegir el mueble se propone su área actual.</p>
            @error('id_ubi') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <fieldset>
        <legend class="form-label">Estado</legend>
        <div class="flex flex-wrap gap-3">
            @foreach ($estados as $estado)
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm has-checked:border-institucional has-checked:bg-institucional/10">
                    <input type="radio" name="estado_resguardo" value="{{ $estado->value }}" class="accent-institucional"
                           @checked(old('estado_resguardo', $resguardo?->estado_resguardo?->value ?? 'activo') === $estado->value)>
                    {{ $estado->label() }}
                </label>
            @endforeach
        </div>
        @error('estado_resguardo') <p class="form-error">{{ $message }}</p> @enderror
    </fieldset>
</div>

<div class="mt-6 flex justify-end gap-2">
    <a href="{{ $resguardo ? route('resguardos.show', $resguardo->id_resguardo) : route('resguardos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">{{ $resguardo ? 'Guardar cambios' : 'Crear resguardo' }}</button>
</div>

@push('scripts')
    <script>
        // Propone como ubicación el área donde está el mueble.
        document.getElementById('id_mueble').addEventListener('change', (evento) => {
            const area = evento.target.selectedOptions[0]?.dataset.area;
            if (area) document.getElementById('id_ubi').value = area;
        });
    </script>
@endpush
