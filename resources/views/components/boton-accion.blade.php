{{--
    Botón de acción con ícono para las tablas.

    Uso:
      <x-boton-accion icono="ver" :href="route('...')" />
      <x-boton-accion icono="editar" :href="route('...')" />
      <x-boton-accion icono="eliminar" :action="route('...')" confirmar="¿Eliminar?" />
--}}
@props([
    'icono',
    'href' => null,
    'action' => null,
    'confirmar' => null,
    'etiqueta' => null,
])

@php
    $iconos = [
        'ver' => ['texto' => 'Ver detalle', 'clases' => 'bg-institucional/10 text-institucional hover:bg-institucional hover:text-white focus-visible:ring-institucional'],
        'editar' => ['texto' => 'Editar', 'clases' => 'bg-dorado/15 text-dorado hover:bg-dorado hover:text-white focus-visible:ring-dorado'],
        'eliminar' => ['texto' => 'Eliminar', 'clases' => 'bg-guinda/10 text-guinda hover:bg-guinda hover:text-white focus-visible:ring-guinda'],
    ];

    $config = $iconos[$icono];
    $etiqueta ??= $config['texto'];
    $clases = 'inline-flex size-9 items-center justify-center rounded-lg transition duration-150 ease-in-out focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:outline-none motion-reduce:transition-none '.$config['clases'];
@endphp

@php
    $svg = match ($icono) {
        'ver' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'editar' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'eliminar' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>',
    };
@endphp

@if ($action)
    <form method="POST" action="{{ $action }}" class="inline-flex"
          @if ($confirmar) onsubmit="return confirm(@js($confirmar))" @endif>
        @csrf
        @method('DELETE')
        <button type="submit" class="{{ $clases }}" title="{{ $etiqueta }}" aria-label="{{ $etiqueta }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="size-4.5" aria-hidden="true">{!! $svg !!}</svg>
        </button>
    </form>
@else
    <a href="{{ $href }}" class="{{ $clases }}" title="{{ $etiqueta }}" aria-label="{{ $etiqueta }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" class="size-4.5" aria-hidden="true">{!! $svg !!}</svg>
    </a>
@endif
