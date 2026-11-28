<?php

namespace App\Http\Controllers;

use App\Enums\EstadoResguardo;
use App\Http\Requests\ResguardoRequest;
use App\Repositories\CatalogoRepository;
use App\Repositories\ResguardoRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResguardoController extends Controller
{
    public function __construct(
        private ResguardoRepository $resguardos,
        private CatalogoRepository $catalogo,
    ) {}

    public function index(Request $request): View
    {
        $estado = EstadoResguardo::tryFrom((string) $request->query('estado'));

        return view('resguardos.index', [
            'resguardos' => $this->resguardos->all($estado, $request->query('buscar')),
            'estadoActual' => $estado,
        ]);
    }

    public function create(): View
    {
        return view('resguardos.create', [
            ...$this->datosFormulario(),
            'folio' => $this->resguardos->siguienteFolio(),
        ]);
    }

    public function store(ResguardoRequest $request): RedirectResponse
    {
        $resguardo = $this->resguardos->create($request->validated());

        return redirect()
            ->route('resguardos.show', $resguardo->id_resguardo)
            ->with('status', "Resguardo {$resguardo->folio_resguardo} creado.");
    }

    public function show(int $resguardo): View
    {
        return view('resguardos.show', [
            'resguardo' => $this->resguardos->findOrFail($resguardo),
        ]);
    }

    public function edit(int $resguardo): View
    {
        return view('resguardos.edit', [
            ...$this->datosFormulario(),
            'resguardo' => $this->resguardos->findOrFail($resguardo),
        ]);
    }

    public function update(ResguardoRequest $request, int $resguardo): RedirectResponse
    {
        $this->resguardos->findOrFail($resguardo);
        $this->resguardos->update($resguardo, $request->validated());

        return redirect()
            ->route('resguardos.show', $resguardo)
            ->with('status', 'Resguardo actualizado.');
    }

    public function destroy(int $resguardo): RedirectResponse
    {
        $folio = $this->resguardos->findOrFail($resguardo)->folio_resguardo;
        $this->resguardos->delete($resguardo);

        return redirect()
            ->route('resguardos.index')
            ->with('status', "Resguardo {$folio} eliminado.");
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(): array
    {
        return [
            'muebles' => $this->catalogo->muebles(),
            'usuarios' => $this->catalogo->usuarios(),
            'areas' => $this->catalogo->areas(),
            'estados' => EstadoResguardo::cases(),
        ];
    }
}
