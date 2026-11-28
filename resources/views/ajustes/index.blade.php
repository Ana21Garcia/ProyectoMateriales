@extends('layouts.app')

@section('eyebrow', 'Mi cuenta')
@section('title', 'Ajustes de cuenta')

@section('content')
    <div class="grid max-w-5xl gap-6 lg:grid-cols-5">
        {{-- Foto de perfil --}}
        <section class="card p-6 lg:col-span-2" aria-labelledby="titulo-foto">
            <h2 id="titulo-foto" class="text-lg font-bold">Foto de perfil</h2>
            <p class="mt-1 text-sm text-slate-500">JPG, PNG o WEBP de máximo 2 MB.</p>

            <div class="mt-6 flex flex-col items-center gap-4">
                <div class="relative">
                    <x-avatar :perfil="$perfilActual" id="foto-actual" class="size-32 text-3xl ring-4 ring-lima" />
                    <img id="foto-previa" alt="Vista previa de la nueva foto"
                         class="hidden size-32 rounded-full object-cover ring-4 ring-institucional">
                </div>
                <div class="text-center">
                    <p class="font-semibold">{{ $perfilActual->nombre_completo }}</p>
                    <p class="text-sm text-slate-500">{{ $perfilActual->area_adscripcion }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('ajustes.foto') }}" enctype="multipart/form-data" class="mt-6 space-y-3">
                @csrf
                <label for="foto" class="btn btn-secondary w-full cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    Elegir imagen
                </label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" class="sr-only">
                <p id="foto-nombre" class="hidden truncate text-center text-xs text-slate-500"></p>
                @error('foto', 'foto') <p class="form-error text-center">{{ $message }}</p> @enderror

                <button type="submit" id="foto-guardar" class="btn btn-primary w-full" disabled>Guardar foto</button>
            </form>

            @if ($perfilActual->foto_url)
                <form method="POST" action="{{ route('ajustes.foto.eliminar') }}" class="mt-3"
                      onsubmit="return confirm('¿Quitar tu foto de perfil?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-sm font-semibold text-guinda hover:underline">Quitar foto</button>
                </form>
            @endif
        </section>

        {{-- Cambio de contraseña --}}
        <section class="card p-6 lg:col-span-3" aria-labelledby="titulo-contrasena">
            <h2 id="titulo-contrasena" class="text-lg font-bold">Cambiar contraseña</h2>
            <p class="mt-1 text-sm text-slate-500">Mínimo 8 caracteres, con al menos una letra y un número.</p>

            <form method="POST" action="{{ route('ajustes.contrasena') }}" id="form-contrasena" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                @foreach ([
                    'contrasena_actual' => ['Contraseña actual', 'current-password'],
                    'contrasena' => ['Nueva contraseña', 'new-password'],
                    'contrasena_confirmation' => ['Confirmar nueva contraseña', 'new-password'],
                ] as $campo => [$etiqueta, $autocompletar])
                    <div>
                        <label for="{{ $campo }}" class="form-label">{{ $etiqueta }}</label>
                        <div class="relative">
                            <input type="password" id="{{ $campo }}" name="{{ $campo }}" required
                                   autocomplete="{{ $autocompletar }}" class="form-input pr-11"
                                   @error($campo, 'contrasena') aria-invalid="true" @enderror>
                            <button type="button" data-mostrar="{{ $campo }}" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-slate-400 transition hover:text-institucional">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error($campo, 'contrasena') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </div>
            </form>
        </section>
    </div>

    {{-- Confirmación antes de cambiar la contraseña --}}
    <dialog id="confirmar-contrasena" aria-labelledby="titulo-confirmar"
            class="m-auto w-full max-w-md rounded-xl p-0 shadow-xl backdrop:bg-oscuro/60">
        <div class="border-t-4 border-lima p-6">
            <div class="flex items-start gap-4">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-institucional/10 text-institucional">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <div>
                    <h3 id="titulo-confirmar" class="text-lg font-bold text-oscuro">¿Cambiar tu contraseña?</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        A partir de ahora deberás usar la nueva contraseña para iniciar sesión.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" id="cancelar-contrasena" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="aceptar-contrasena" class="btn btn-primary">Sí, cambiarla</button>
            </div>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        (() => {
            // Vista previa de la foto antes de guardarla.
            const inputFoto = document.getElementById('foto');
            const previa = document.getElementById('foto-previa');
            const actual = document.getElementById('foto-actual');
            const nombre = document.getElementById('foto-nombre');
            const guardar = document.getElementById('foto-guardar');

            inputFoto.addEventListener('change', () => {
                const archivo = inputFoto.files[0];
                guardar.disabled = !archivo;
                nombre.classList.toggle('hidden', !archivo);
                previa.classList.toggle('hidden', !archivo);
                actual.classList.toggle('hidden', !!archivo);
                if (!archivo) return;
                nombre.textContent = archivo.name;
                previa.src = URL.createObjectURL(archivo);
            });

            // Mostrar u ocultar contraseñas.
            document.querySelectorAll('[data-mostrar]').forEach((boton) => {
                boton.addEventListener('click', () => {
                    const campo = document.getElementById(boton.dataset.mostrar);
                    const visible = campo.type === 'text';
                    campo.type = visible ? 'password' : 'text';
                    boton.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
                });
            });

            // Pedir confirmación antes de enviar el cambio de contraseña.
            const form = document.getElementById('form-contrasena');
            const dialogo = document.getElementById('confirmar-contrasena');
            let confirmado = false;

            form.addEventListener('submit', (evento) => {
                if (confirmado) return;
                evento.preventDefault();
                dialogo.showModal();
            });
            document.getElementById('cancelar-contrasena').addEventListener('click', () => dialogo.close());
            document.getElementById('aceptar-contrasena').addEventListener('click', () => {
                confirmado = true;
                dialogo.close();
                form.requestSubmit();
            });
        })();
    </script>
@endpush
