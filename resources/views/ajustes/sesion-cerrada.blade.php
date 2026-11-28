<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión cerrada · Recursos Materiales</title>
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col bg-fondo font-sans text-oscuro antialiased">
    <div class="h-1 bg-lima"></div>

    <main class="flex flex-1 items-center justify-center px-4">
        <div class="card w-full max-w-sm p-8 text-center">
            <span class="mx-auto flex size-14 items-center justify-center rounded-xl bg-lima text-lg font-bold text-oscuro">RM</span>
            <h1 class="mt-5 text-2xl font-bold">Sesión cerrada</h1>
            <p class="mt-2 text-sm text-slate-500">Cerraste tu sesión correctamente.</p>
            <a href="{{ route('movimientos.index') }}" class="btn btn-primary mt-6 w-full">Volver a entrar</a>
        </div>
    </main>

    <footer class="bg-oscuro text-white">
        <div class="h-1 bg-lima"></div>
        <p class="px-4 py-3 text-center text-sm">© 2026 · Departamento de Recursos y Materiales</p>
    </footer>
</body>
</html>
