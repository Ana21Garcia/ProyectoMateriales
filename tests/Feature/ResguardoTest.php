<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class ResguardoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_las_vistas_de_resguardos_cargan(): void
    {
        $this->get(route('resguardos.index'))->assertOk()->assertSee('RES-2026-0001');
        $this->get(route('resguardos.index', ['buscar' => 'proyector']))->assertOk()->assertDontSee('RES-2026-0001');
        $this->get(route('resguardos.create'))->assertOk()->assertSee('RES-2026-0006');
        $this->get(route('resguardos.show', 1))->assertOk()->assertSee('Escritorio ejecutivo');
        $this->get(route('resguardos.edit', 1))->assertOk();
    }

    public function test_crea_actualiza_y_elimina_un_resguardo(): void
    {
        $this->post(route('resguardos.store'), [
            'id_mueble' => 4,
            'id_usuario' => 3,
            'id_ubi' => 5,
            'fech_resguardo' => '2026-11-26',
            'estado_resguardo' => 'activo',
        ])->assertRedirect(route('resguardos.show', 6));

        $this->get(route('resguardos.show', 6))->assertSee('RES-2026-0006')->assertSee('Proyector');

        $this->put(route('resguardos.update', 6), [
            'id_mueble' => 4,
            'id_usuario' => 2,
            'id_ubi' => 5,
            'fech_resguardo' => '2026-11-26',
            'estado_resguardo' => 'liberado',
        ])->assertRedirect(route('resguardos.show', 6));

        $this->get(route('resguardos.show', 6))->assertSee('Liberado')->assertSee('Jorge');

        $this->delete(route('resguardos.destroy', 6))->assertRedirect(route('resguardos.index'));
        $this->get(route('resguardos.show', 6))->assertNotFound();
    }

    public function test_un_mueble_solo_tiene_un_resguardo_activo(): void
    {
        // El mueble 1 ya tiene el resguardo activo RES-2026-0001.
        $this->post(route('resguardos.store'), [
            'id_mueble' => 1, 'id_usuario' => 2, 'id_ubi' => 1, 'fech_resguardo' => '2026-11-26', 'estado_resguardo' => 'activo',
        ])->assertSessionHasErrors('id_mueble');

        // Editar el mismo resguardo sí se permite.
        $this->put(route('resguardos.update', 1), [
            'id_mueble' => 1, 'id_usuario' => 2, 'id_ubi' => 1, 'fech_resguardo' => '2026-11-26', 'estado_resguardo' => 'activo',
        ])->assertSessionHasNoErrors();
    }

    public function test_valida_campos_requeridos(): void
    {
        $this->post(route('resguardos.store'), [])
            ->assertSessionHasErrors(['id_mueble', 'id_usuario', 'id_ubi', 'fech_resguardo', 'estado_resguardo']);
    }
}
