<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambiarContrasenaRequest;
use App\Repositories\PerfilRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AjustesController extends Controller
{
    public function __construct(private PerfilRepository $perfil) {}

    public function index(): View
    {
        return view('ajustes.index');
    }

    public function actualizarFoto(Request $request): RedirectResponse
    {
        $request->validateWithBag('foto', [
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto.required' => 'Selecciona una imagen.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'foto.max' => 'La foto no debe pesar más de 2 MB.',
        ]);

        $ruta = $request->file('foto')->store('fotos-perfil', $this->perfil->disco());
        $this->perfil->cambiarFoto($ruta);

        return redirect()->route('ajustes.index')->with('status', 'Foto de perfil actualizada.');
    }

    public function eliminarFoto(): RedirectResponse
    {
        $this->perfil->quitarFoto();

        return redirect()->route('ajustes.index')->with('status', 'Foto de perfil eliminada.');
    }

    public function actualizarContrasena(CambiarContrasenaRequest $request): RedirectResponse
    {
        $this->perfil->cambiarContrasena($request->validated('contrasena'));

        return redirect()->route('ajustes.index')->with('status', 'Contraseña actualizada correctamente.');
    }

    /**
     * Al conectar el inicio de sesión real, agregar Auth::logout() antes de invalidar.
     */
    public function cerrarSesion(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sesion.cerrada');
    }

    public function sesionCerrada(): View
    {
        return view('ajustes.sesion-cerrada');
    }
}
