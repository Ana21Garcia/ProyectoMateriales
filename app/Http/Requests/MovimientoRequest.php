<?php

namespace App\Http\Requests;

use App\Enums\TipoMovimiento;
use App\Repositories\CatalogoRepository;
use App\Repositories\MovimientoRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $catalogo = app(CatalogoRepository::class);

        // Al conectar la base: Rule::exists('muebles', 'id_mueble') y Rule::exists('areas', 'id_area').
        return [
            'id_mueble' => ['required', 'integer', Rule::in($catalogo->muebles()->keys())],
            'id_area_destino' => ['required', 'integer', Rule::in($catalogo->areas()->keys())],
            'tipo_mov' => ['required', Rule::enum(TipoMovimiento::class)],
            'fech_mov' => ['required', 'date'],
            'observaciones_mov' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Reglas que dependen del estado actual del mueble.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $mueble = app(CatalogoRepository::class)->mueble($this->integer('id_mueble'));
                $idMovActual = $this->route('movimiento') ? (int) $this->route('movimiento') : null;

                if ($mueble->estado_mueble === 'Baja') {
                    $validator->errors()->add('id_mueble', 'Este mueble está dado de baja y no se puede mover.');
                }

                if ($mueble->id_area === $this->integer('id_area_destino')) {
                    $validator->errors()->add('id_area_destino', 'El área destino debe ser distinta al área actual del mueble.');
                }

                if (app(MovimientoRepository::class)->muebleTieneMovimientoEnCurso($mueble->id_mueble, $idMovActual)) {
                    $validator->errors()->add('id_mueble', 'Este mueble ya tiene un movimiento en curso.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id_mueble' => 'mueble',
            'id_area_destino' => 'área destino',
            'tipo_mov' => 'tipo de movimiento',
            'fech_mov' => 'fecha del movimiento',
            'observaciones_mov' => 'observaciones',
        ];
    }
}
