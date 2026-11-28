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
            <a href="{{ route('movimientos.index') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-lg bg-lima font-bold text-oscuro">RM</span>
                <span>
                    <span class="block text-xs font-semibold tracking-widest text-lima uppercase">Inventario</span>
                    <span class="block text-lg leading-tight font-bold">Recursos Materiales</span>
                </span>
            </a>

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
        <div class="h-1 bg-linear-to-r from-lima via-institucional to-guinda"></div>
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
        <div class="h-1 bg-linear-to-r from-lima via-institucional to-guinda"></div>
        <p class="mx-auto max-w-7xl px-4 py-3 text-center text-sm sm:px-6">
            © 2026 · Departamento de Recursos y Materiales
        </p>
    </footer>

    @stack('scripts')
</body>
</html>
