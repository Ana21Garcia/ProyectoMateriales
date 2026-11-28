<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;

/**
 * Perfil del usuario que tiene la sesión abierta.
 *
 * Mientras no exista inicio de sesión real se usa un usuario de ejemplo
 * guardado en sesión (contraseña de prueba: materiales2026). Al conectar la
 * base de datos estos métodos se sustituyen por auth()->user() y el modelo
 * Usuario (columnas contra_usuario y foto_usuario).
 */
class PerfilRepository
{
    public const CONTRASENA_DEMO = 'materiales2026';

    private const KEY = 'demo.perfil';

    private const DISCO = 'public';

    public function __construct(private CatalogoRepository $catalogo) {}

    public function actual(): Fluent
    {
        $perfil = $this->datos();
        $usuario = $this->catalogo->usuario($perfil['id_usuario']);

        return new Fluent([
            ...$usuario->toArray(),
            'foto_url' => $perfil['foto_usuario'] ? Storage::disk(self::DISCO)->url($perfil['foto_usuario']) : null,
            'iniciales' => mb_substr($usuario->nombre_usuario, 0, 1).mb_substr($usuario->apaterno_usuario, 0, 1),
        ]);
    }

    public function verificarContrasena(string $contrasena): bool
    {
        return Hash::check($contrasena, $this->datos()['contra_usuario']);
    }

    public function cambiarContrasena(string $nueva): void
    {
        $this->guardar(['contra_usuario' => Hash::make($nueva)]);
    }

    /**
     * Guarda la nueva foto y borra la anterior.
     */
    public function cambiarFoto(string $ruta): void
    {
        $this->borrarArchivoFoto();
        $this->guardar(['foto_usuario' => $ruta]);
    }

    public function quitarFoto(): void
    {
        $this->borrarArchivoFoto();
        $this->guardar(['foto_usuario' => null]);
    }

    public function disco(): string
    {
        return self::DISCO;
    }

    private function borrarArchivoFoto(): void
    {
        $actual = $this->datos()['foto_usuario'];

        if ($actual) {
            Storage::disk(self::DISCO)->delete($actual);
        }
    }

    /**
     * @return array{id_usuario: int, contra_usuario: string, foto_usuario: ?string}
     */
    private function datos(): array
    {
        if (! session()->has(self::KEY)) {
            session()->put(self::KEY, [
                'id_usuario' => 1,
                'contra_usuario' => Hash::make(self::CONTRASENA_DEMO),
                'foto_usuario' => null,
            ]);
        }

        return session(self::KEY);
    }

    /**
     * @param  array<string, mixed>  $cambios
     */
    private function guardar(array $cambios): void
    {
        session()->put(self::KEY, [...$this->datos(), ...$cambios]);
    }
}
