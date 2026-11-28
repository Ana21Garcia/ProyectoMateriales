<?php

namespace Tests\Feature;

use App\Repositories\PerfilRepository;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AjustesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
        Storage::fake('public');
    }

    public function test_la_pagina_de_ajustes_y_el_menu_cargan(): void
    {
        $this->get(route('movimientos.index'))
            ->assertOk()
            ->assertSee('Ajustes de cuenta')
            ->assertSee('Cerrar sesión');

        $this->get(route('ajustes.index'))
            ->assertOk()
            ->assertSee('Foto de perfil')
            ->assertSee('Cambiar contraseña')
            ->assertSee('María López Hernández');
    }

    public function test_cambia_la_contrasena(): void
    {
        $this->put(route('ajustes.contrasena'), [
            'contrasena_actual' => PerfilRepository::CONTRASENA_DEMO,
            'contrasena' => 'nuevaClave123',
            'contrasena_confirmation' => 'nuevaClave123',
        ])->assertRedirect(route('ajustes.index'))->assertSessionHasNoErrors();

        $this->assertTrue(app(PerfilRepository::class)->verificarContrasena('nuevaClave123'));
    }

    public function test_valida_el_cambio_de_contrasena(): void
    {
        $this->put(route('ajustes.contrasena'), [
            'contrasena_actual' => 'incorrecta1',
            'contrasena' => 'nuevaClave123',
            'contrasena_confirmation' => 'nuevaClave123',
        ])->assertSessionHasErrorsIn('contrasena', 'contrasena_actual');

        $this->put(route('ajustes.contrasena'), [
            'contrasena_actual' => PerfilRepository::CONTRASENA_DEMO,
            'contrasena' => 'nuevaClave123',
            'contrasena_confirmation' => 'otraClave123',
        ])->assertSessionHasErrorsIn('contrasena', 'contrasena');

        $this->put(route('ajustes.contrasena'), [
            'contrasena_actual' => PerfilRepository::CONTRASENA_DEMO,
            'contrasena' => 'corta',
            'contrasena_confirmation' => 'corta',
        ])->assertSessionHasErrorsIn('contrasena', 'contrasena');

        $this->assertTrue(app(PerfilRepository::class)->verificarContrasena(PerfilRepository::CONTRASENA_DEMO));
    }

    public function test_cambia_y_quita_la_foto_de_perfil(): void
    {
        $this->post(route('ajustes.foto'), ['foto' => UploadedFile::fake()->image('yo.jpg')])
            ->assertRedirect(route('ajustes.index'));

        $archivos = Storage::disk('public')->files('fotos-perfil');
        $this->assertCount(1, $archivos);
        $this->get(route('ajustes.index'))->assertSee($archivos[0]);

        $this->delete(route('ajustes.foto.eliminar'))->assertRedirect(route('ajustes.index'));
        $this->assertCount(0, Storage::disk('public')->files('fotos-perfil'));
    }

    public function test_rechaza_archivos_que_no_son_imagen(): void
    {
        $this->post(route('ajustes.foto'), ['foto' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')])
            ->assertSessionHasErrorsIn('foto', 'foto');
    }

    public function test_cerrar_sesion(): void
    {
        $this->post(route('cerrar-sesion'))->assertRedirect(route('sesion.cerrada'));
        $this->get(route('sesion.cerrada'))->assertOk()->assertSee('Sesión cerrada');
    }
}
