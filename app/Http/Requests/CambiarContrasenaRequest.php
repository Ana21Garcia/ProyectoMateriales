<?php

namespace App\Http\Requests;

use App\Repositories\PerfilRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class CambiarContrasenaRequest extends FormRequest
{
    /**
     * Los errores se muestran en la sección de contraseña de la página de ajustes.
     */
    protected $errorBag = 'contrasena';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'contrasena_actual' => ['required', 'string'],
            'contrasena' => ['required', 'string', 'confirmed', 'different:contrasena_actual', Password::min(8)->letters()->numbers()],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (filled($this->input('contrasena_actual'))
                    && ! app(PerfilRepository::class)->verificarContrasena($this->input('contrasena_actual'))) {
                    $validator->errors()->add('contrasena_actual', 'La contraseña actual no es correcta.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contrasena_actual.required' => 'Escribe tu contraseña actual.',
            'contrasena.required' => 'Escribe la nueva contraseña.',
            'contrasena.confirmed' => 'La confirmación no coincide con la nueva contraseña.',
            'contrasena.different' => 'La nueva contraseña debe ser distinta a la actual.',
            'contrasena.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'contrasena.letters' => 'La nueva contraseña debe incluir al menos una letra.',
            'contrasena.numbers' => 'La nueva contraseña debe incluir al menos un número.',
        ];
    }
}
