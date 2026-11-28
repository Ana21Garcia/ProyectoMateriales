{{-- Foto de perfil del usuario, o sus iniciales si no tiene foto. --}}
@props(['perfil'])

@if ($perfil->foto_url)
    <img src="{{ $perfil->foto_url }}" alt="Foto de {{ $perfil->nombre_completo }}"
         {{ $attributes->merge(['class' => 'shrink-0 rounded-full object-cover']) }}>
@else
    <span {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center rounded-full bg-oscuro font-bold text-lima']) }}
          aria-hidden="true">{{ $perfil->iniciales }}</span>
@endif
