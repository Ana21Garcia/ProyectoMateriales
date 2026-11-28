{{--
    Barra de búsqueda y filtro por estado para los listados.
    - La lupa envía la búsqueda (también funciona con Enter).
    - Al elegir un estado se filtra automáticamente.
    Variables: $ruta, $placeholder, $estados (casos del enum) y $estadoActual.
--}}
<form method="GET" action="{{ $ruta }}" class="flex flex-wrap items-end gap-3 border-b border-slate-200 p-4" role="search">
    <div class="min-w-60 flex-1">
        <label for="buscar" class="form-label">Buscar</label>
        <div class="relative">
            <input type="search" id="buscar" name="buscar" value="{{ request('buscar') }}"
                   class="form-input pr-12" placeholder="{{ $placeholder }}">
            <button type="submit" aria-label="Buscar" title="Buscar"
                    class="absolute inset-y-1 right-1 flex w-10 items-center justify-center rounded-md bg-institucional text-white transition duration-150 ease-in-out hover:bg-oscuro focus-visible:ring-2 focus-visible:ring-institucional focus-visible:ring-offset-1 focus-visible:outline-none motion-reduce:transition-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="m20 20-3.5-3.5" />
                </svg>
            </button>
        </div>
    </div>

    <div class="w-48">
        <label for="estado" class="form-label">Estado</label>
        <select id="estado" name="estado" class="form-input" onchange="this.form.requestSubmit()">
            <option value="">Todos</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado->value }}" @selected($estadoActual === $estado)>{{ $estado->label() }}</option>
            @endforeach
        </select>
    </div>

    @if (filled(request('buscar')) || $estadoActual)
        <a href="{{ $ruta }}" class="btn btn-secondary">Limpiar</a>
    @endif
</form>
