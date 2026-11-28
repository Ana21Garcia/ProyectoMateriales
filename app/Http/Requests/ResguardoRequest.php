<?php

namespace App\Http\Requests;

use App\Enums\EstadoResguardo;
use App\Repositories\CatalogoRepository;
use App\Repositories\ResguardoRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ResguardoRequest extends FormRequest
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

        // Al conectar la base: Rule::exists('muebles', 'id_mueble'), Rule::exists('usuarios', 'id_usuario')...
        return [
            'id_mueble' => ['required', 'integer', Rule::in($catalogo->muebles()->keys())],
            'id_usuario' => ['required', 'integer', Rule::in($catalogo->usuarios()->keys())],
            'id_ubi' => ['required', 'integer', Rule::in($catalogo->areas()->keys())],
            'fech_resguardo' => ['required', 'date'],
            'estado_resguardo' => ['required', Rule::enum(EstadoResguardo::class)],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty() || $this->input('estado_resguardo') !== EstadoResguardo::Activo->value) {
                    return;
                }

                $idActual = $this->route('resguardo') ? (int) $this->route('resguardo') : null;

                if (app(ResguardoRepository::class)->muebleTieneResguardoActivo($this->integer('id_mueble'), $idActual)) {
                    $validator->errors()->add('id_mueble', 'Este mueble ya tiene un resguardo activo.');
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
            'id_usuario' => 'responsable',
            'id_ubi' => 'ubicación',
            'fech_resguardo' => 'fecha del resguardo',
            'estado_resguardo' => 'estado',
        ];
    }
}
