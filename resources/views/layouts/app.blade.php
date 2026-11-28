<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventario') · Recursos Materiales</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-fondo font-sans text-oscuro antialiased">
    @php
        $menu = [
            ['ruta' => 'movimientos.index', 'patron' => 'movimientos.*', 'texto' => 'Movimientos'],
            ['ruta' => 'resguardos.index', 'patron' => 'resguardos.*', 'texto' => 'Resguardos'],
        ];
    @endphp

    <header class="bg-oscuro text-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                {{-- Menú de ajustes --}}
                <details id="menu-ajustes" class="group relative">
                    <summary class="flex size-10 cursor-pointer list-none items-center justify-center rounded-lg text-white/80 transition duration-150 ease-in-out hover:bg-white/10 hover:text-white focus-visible:ring-2 focus-visible:ring-lima focus-visible:outline-none group-open:bg-white/15 group-open:text-white motion-reduce:transition-none [&::-webkit-details-marker]:hidden"
                             title="Ajustes" aria-label="Ajustes">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="size-5 transition-transform duration-300 group-open:rotate-90 motion-reduce:transition-none" aria-hidden="true">
                            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </summary>

                    <div class="absolute top-full left-0 z-20 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white text-oscuro shadow-lg">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3">
                            <x-avatar :perfil="$perfilActual" class="size-10 text-sm" />
                            <div class="min-w-0 text-sm">
                                <p class="truncate font-semibold">{{ $perfilActual->nombre_completo }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $perfilActual->correo_usuario }}</p>
                            </div>
                        </div>
                        <a href="{{ route('ajustes.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition hover:bg-slate-50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-institucional" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                            Ajustes de cuenta
                        </a>
                        <form method="POST" action="{{ route('cerrar-sesion') }}" class="border-t border-slate-100">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-guinda transition hover:bg-guinda/5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </details>

                <a href="{{ route('movimientos.index') }}" class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-lima font-bold text-oscuro">RM</span>
                    <span>
                        <span class="block text-xs font-semibold tracking-widest text-lima uppercase">Inventario</span>
                        <span class="block text-lg leading-tight font-bold">Recursos Materiales</span>
                    </span>
                </a>
            </div>

            <nav class="flex gap-1">
                @foreach ($menu as $item)
                    <a href="{{ route($item['ruta']) }}"
                       @class([
                           'rounded-lg px-4 py-2 text-sm font-semibold transition',
                           'bg-white/15 text-white' => request()->routeIs($item['patron']),
                           'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs($item['patron']),
                       ])>
                        {{ $item['texto'] }}
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="h-1 bg-lima"></div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold tracking-widest text-institucional uppercase">@yield('eyebrow')</p>
                <h1 class="mt-1 text-3xl font-bold text-oscuro">@yield('title')</h1>
                <div class="mt-2 h-1 w-16 rounded bg-lima"></div>
            </div>
            <div class="flex flex-wrap gap-2">
                @yield('actions')
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg border-l-4 border-lima bg-lima/20 px-4 py-3 text-sm font-medium text-oscuro" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border-l-4 border-guinda bg-guinda/10 px-4 py-3 text-sm font-medium text-guinda" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-oscuro text-white">
        <div class="h-1 bg-lima"></div>
        <p class="mx-auto max-w-7xl px-4 py-3 text-center text-sm sm:px-6">
            © 2026 · Departamento de Recursos y Materiales
        </p>
    </footer>

    <script>
        // Cierra el menú de ajustes al hacer clic fuera de él o al presionar Escape.
        (() => {
            const menu = document.getElementById('menu-ajustes');
            document.addEventListener('click', (e) => { if (!menu.contains(e.target)) menu.open = false; });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') menu.open = false; });
        })();
    </script>
    @stack('scripts')
</body>
</html>
