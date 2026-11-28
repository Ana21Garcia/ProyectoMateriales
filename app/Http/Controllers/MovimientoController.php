<?php

namespace App\Http\Controllers;

use App\Enums\EstadoMovimiento;
use App\Enums\TipoMovimiento;
use App\Http\Requests\MovimientoRequest;
use App\Repositories\CatalogoRepository;
use App\Repositories\MovimientoRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MovimientoController extends Controller
{
    public function __construct(
        private MovimientoRepository $movimientos,
        private CatalogoRepository $catalogo,
    ) {}

    public function index(Request $request): View
    {
        $estado = EstadoMovimiento::tryFrom((string) $request->query('estado'));

        return view('movimientos.index', [
            'movimientos' => $this->movimientos->all($estado, $request->query('buscar')),
            'estadoActual' => $estado,
        ]);
    }

    public function create(): View
    {
        return view('movimientos.create', $this->datosFormulario());
    }

    public function store(MovimientoRequest $request): RedirectResponse
    {
        $movimiento = $this->movimientos->create($request->validated());

        return redirect()
            ->route('movimientos.show', $movimiento->id_mov)
            ->with('status', 'Movimiento registrado. Queda en curso hasta que se valide.');
    }

    public function show(int $movimiento): View
    {
        return view('movimientos.show', [
            'movimiento' => $this->movimientos->findOrFail($movimiento),
        ]);
    }

    public function edit(int $movimiento): View|RedirectResponse
    {
        $registro = $this->movimientos->findOrFail($movimiento);

        if ($registro->estado_mov->esFinal()) {
            return redirect()
                ->route('movimientos.show', $movimiento)
                ->with('error', 'Solo se pueden editar movimientos en curso.');
        }

        return view('movimientos.edit', [
            ...$this->datosFormulario(),
            'movimiento' => $registro,
        ]);
    }

    public function update(MovimientoRequest $request, int $movimiento): RedirectResponse
    {
        if ($this->movimientos->findOrFail($movimiento)->estado_mov->esFinal()) {
            return redirect()
                ->route('movimientos.show', $movimiento)
                ->with('error', 'Solo se pueden editar movimientos en curso.');
        }

        $this->movimientos->update($movimiento, $request->validated());

        return redirect()
            ->route('movimientos.show', $movimiento)
            ->with('status', 'Movimiento actualizado.');
    }

    /**
     * Valida un movimiento en curso: lo completa, lo rechaza o da de baja el mueble.
     */
    public function cambiarEstado(Request $request, int $movimiento): RedirectResponse
    {
        $validated = $request->validate([
            'estado_mov' => ['required', Rule::enum(EstadoMovimiento::class)->except([EstadoMovimiento::EnCurso])],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->movimientos->findOrFail($movimiento)->estado_mov->esFinal()) {
            return back()->with('error', 'Este movimiento ya fue cerrado.');
        }

        $estado = EstadoMovimiento::from($validated['estado_mov']);
        $this->movimientos->cambiarEstado($movimiento, $estado, $validated['observaciones'] ?? null);

        return redirect()
            ->route('movimientos.show', $movimiento)
            ->with('status', "Movimiento marcado como «{$estado->label()}».");
    }

    public function destroy(int $movimiento): RedirectResponse
    {
        if ($this->movimientos->findOrFail($movimiento)->estado_mov->esFinal()) {
            return back()->with('error', 'Los movimientos cerrados se conservan como historial y no se pueden eliminar.');
        }

        $this->movimientos->delete($movimiento);

        return redirect()
            ->route('movimientos.index')
            ->with('status', 'Movimiento eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFormulario(): array
    {
        return [
            'muebles' => $this->catalogo->muebles(),
            'areas' => $this->catalogo->areas(),
            'tipos' => TipoMovimiento::cases(),
        ];
    }
}
