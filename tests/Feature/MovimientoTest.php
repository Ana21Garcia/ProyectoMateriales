<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class MovimientoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_las_vistas_de_movimientos_cargan(): void
    {
        $this->get('/')->assertRedirect('/movimientos');

        $this->get(route('movimientos.index'))
            ->assertOk()
            ->assertSee('Proyector')
            ->assertSee('En curso');

        $this->get(route('movimientos.index', ['estado' => 'rechazada']))
            ->assertOk()
            ->assertSee('Campana de extracción')
            ->assertDontSee('Proyector');

        $this->get(route('movimientos.index', ['buscar' => 'audiovisual']))
            ->assertOk()
            ->assertSee('Proyector')
            ->assertDontSee('Campana de extracción');

        $this->get(route('movimientos.create'))->assertOk()->assertSee('Información del mueble');
        $this->get(route('movimientos.show', 2))->assertOk()->assertSee('Validar movimiento');
        $this->get(route('movimientos.edit', 2))->assertOk();
    }

    public function test_registra_un_movimiento_en_curso(): void
    {
        $this->post(route('movimientos.store'), [
            'id_mueble' => 1,
            'id_area_destino' => 6,
            'tipo_mov' => 'traslado',
            'fech_mov' => '2026-11-26',
            'observaciones_mov' => 'Prueba',
        ])->assertRedirect(route('movimientos.show', 6));

        $this->get(route('movimientos.show', 6))
            ->assertSee('Escritorio ejecutivo')
            ->assertSee('Centro de Cómputo')
            ->assertSee('En curso');
    }

    public function test_valida_destino_distinto_y_un_solo_movimiento_en_curso(): void
    {
        // El mueble 1 está en el área 1.
        $this->post(route('movimientos.store'), [
            'id_mueble' => 1, 'id_area_destino' => 1, 'tipo_mov' => 'traslado', 'fech_mov' => '2026-11-26',
        ])->assertSessionHasErrors('id_area_destino');

        // El mueble 4 ya tiene el movimiento 2 en curso.
        $this->post(route('movimientos.store'), [
            'id_mueble' => 4, 'id_area_destino' => 1, 'tipo_mov' => 'traslado', 'fech_mov' => '2026-11-26',
        ])->assertSessionHasErrors('id_mueble');

        // El mueble 7 está dado de baja.
        $this->post(route('movimientos.store'), [
            'id_mueble' => 7, 'id_area_destino' => 1, 'tipo_mov' => 'traslado', 'fech_mov' => '2026-11-26',
        ])->assertSessionHasErrors('id_mueble');
    }

    public function test_completar_mueve_el_mueble_al_area_destino(): void
    {
        $this->patch(route('movimientos.estado', 2), ['estado_mov' => 'completada'])
            ->assertRedirect(route('movimientos.show', 2));

        $this->get(route('movimientos.show', 2))
            ->assertSee('Completada')
            ->assertDontSee('Validar movimiento');

        // El proyector ahora está en Servicios Escolares (área 3), así que no puede ir ahí otra vez.
        $this->post(route('movimientos.store'), [
            'id_mueble' => 4, 'id_area_destino' => 3, 'tipo_mov' => 'traslado', 'fech_mov' => '2026-11-26',
        ])->assertSessionHasErrors('id_area_destino');
    }

    public function test_un_movimiento_cerrado_no_se_edita_ni_se_elimina(): void
    {
        $this->get(route('movimientos.edit', 1))->assertRedirect(route('movimientos.show', 1));
        $this->delete(route('movimientos.destroy', 1))->assertSessionHas('error');
        $this->patch(route('movimientos.estado', 1), ['estado_mov' => 'baja'])->assertSessionHas('error');
        $this->patch(route('movimientos.estado', 2), ['estado_mov' => 'en_curso'])->assertSessionHasErrors('estado_mov');
    }

    public function test_elimina_un_movimiento_en_curso(): void
    {
        $this->delete(route('movimientos.destroy', 3))->assertRedirect(route('movimientos.index'));
        $this->get(route('movimientos.show', 3))->assertNotFound();
    }
}
