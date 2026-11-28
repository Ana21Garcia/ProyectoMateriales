<?php

namespace App\Repositories;

use App\Enums\EstadoMovimiento;
use App\Enums\TipoMovimiento;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

/**
 * Movimientos de muebles entre áreas (tabla Movimientos).
 *
 * Columnas según el diagrama: id_mov, fech_mov, tipo_mov, fechval_mov,
 * estado_mov y observaciones_mov. Se agregan id_mueble, id_area_origen e
 * id_area_destino para saber qué mueble se mueve y hacia dónde.
 */
class MovimientoRepository extends SessionRepository
{
    public function __construct(private CatalogoRepository $catalogo) {}

    protected function key(): string
    {
        return 'demo.movimientos';
    }

    protected function primaryKey(): string
    {
        return 'id_mov';
    }

    /**
     * @return Collection<int, Fluent>
     */
    public function all(?EstadoMovimiento $estado = null, ?string $buscar = null): Collection
    {
        return collect($this->rows())
            ->map(fn (array $row) => $this->hydrate($row))
            ->when($estado, fn (Collection $items) => $items->filter(fn (Fluent $mov) => $mov->estado_mov === $estado))
            ->when($buscar, fn (Collection $items) => $items->filter(fn (Fluent $mov) => str_contains(
                mb_strtolower(implode(' ', [
                    $mov->mueble?->nombre_mueble,
                    $mov->mueble?->codigo_mueble,
                    $mov->mueble?->categoria,
                    $mov->mueble?->marca_mueble,
                    $mov->tipo_mov->label(),
                    $mov->area_origen?->nombre_area,
                    $mov->area_destino?->nombre_area,
                ])),
                mb_strtolower($buscar),
            )))
            ->sortByDesc('fech_mov')
            ->values();
    }

    public function findOrFail(int $id): Fluent
    {
        return $this->hydrate($this->findRowOrFail($id));
    }

    /**
     * Indica si el mueble ya tiene otro movimiento en curso.
     */
    public function muebleTieneMovimientoEnCurso(int $idMueble, ?int $exceptoIdMov = null): bool
    {
        return collect($this->rows())->contains(fn (array $row) => $row['id_mueble'] === $idMueble
            && $row['estado_mov'] === EstadoMovimiento::EnCurso->value
            && $row['id_mov'] !== $exceptoIdMov);
    }

    /**
     * Registra un movimiento nuevo. Siempre inicia "En curso" y toma como
     * origen el área actual del mueble.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Fluent
    {
        $mueble = $this->catalogo->mueble((int) $data['id_mueble']);

        return $this->hydrate($this->insertRow([
            ...$this->normalizar($data),
            'id_area_origen' => $mueble->id_area,
            'estado_mov' => EstadoMovimiento::EnCurso->value,
            'fechval_mov' => null,
        ]));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): Fluent
    {
        $mueble = $this->catalogo->mueble((int) $data['id_mueble']);

        return $this->hydrate($this->updateRow($id, [
            ...$this->normalizar($data),
            'id_area_origen' => $mueble->id_area,
        ]));
    }

    /**
     * Cierra un movimiento en curso y aplica el efecto sobre el mueble:
     * - Completada: el mueble pasa al área destino.
     * - Baja: el mueble queda marcado como dado de baja.
     * - Rechazada: el mueble no cambia.
     */
    public function cambiarEstado(int $id, EstadoMovimiento $estado, ?string $observaciones = null): Fluent
    {
        $movimiento = $this->findRowOrFail($id);

        $cambios = [
            'estado_mov' => $estado->value,
            'fechval_mov' => now()->toDateString(),
        ];

        if (filled($observaciones)) {
            $cambios['observaciones_mov'] = trim(($movimiento['observaciones_mov'] ?? '')."\n".$observaciones);
        }

        match ($estado) {
            EstadoMovimiento::Completada => $this->catalogo->moverMueble($movimiento['id_mueble'], $movimiento['id_area_destino']),
            EstadoMovimiento::Baja => $this->catalogo->cambiarEstadoMueble($movimiento['id_mueble'], 'Baja'),
            default => null,
        };

        return $this->hydrate($this->updateRow($id, $cambios));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizar(array $data): array
    {
        return [
            'id_mueble' => (int) $data['id_mueble'],
            'id_area_destino' => (int) $data['id_area_destino'],
            'tipo_mov' => $data['tipo_mov'],
            'fech_mov' => $data['fech_mov'],
            'observaciones_mov' => $data['observaciones_mov'] ?? null,
        ];
    }

    private function hydrate(array $row): Fluent
    {
        return new Fluent([
            ...$row,
            'estado_mov' => EstadoMovimiento::from($row['estado_mov']),
            'tipo_mov' => TipoMovimiento::from($row['tipo_mov']),
            'mueble' => $this->catalogo->mueble($row['id_mueble']),
            'area_origen' => $this->catalogo->area($row['id_area_origen']),
            'area_destino' => $this->catalogo->area($row['id_area_destino']),
        ]);
    }

    protected function seed(): array
    {
        return [
            ['id_mov' => 1, 'fech_mov' => '2026-11-03', 'tipo_mov' => 'traslado', 'fechval_mov' => '2026-11-05', 'estado_mov' => 'completada', 'observaciones_mov' => 'Traslado por reorganización de oficinas.', 'id_mueble' => 2, 'id_area_origen' => 1, 'id_area_destino' => 2],
            ['id_mov' => 2, 'fech_mov' => '2026-11-18', 'tipo_mov' => 'prestamo', 'fechval_mov' => null, 'estado_mov' => 'en_curso', 'observaciones_mov' => 'Préstamo para evento de aniversario.', 'id_mueble' => 4, 'id_area_origen' => 5, 'id_area_destino' => 3],
            ['id_mov' => 3, 'fech_mov' => '2026-11-20', 'tipo_mov' => 'mantenimiento', 'fechval_mov' => null, 'estado_mov' => 'en_curso', 'observaciones_mov' => 'Revisión de fuente de poder.', 'id_mueble' => 3, 'id_area_origen' => 6, 'id_area_destino' => 7],
            ['id_mov' => 4, 'fech_mov' => '2026-10-28', 'tipo_mov' => 'reasignacion', 'fechval_mov' => '2026-10-30', 'estado_mov' => 'rechazada', 'observaciones_mov' => 'El área destino no cuenta con espacio.', 'id_mueble' => 6, 'id_area_origen' => 4, 'id_area_destino' => 5],
            ['id_mov' => 5, 'fech_mov' => '2026-10-15', 'tipo_mov' => 'traslado', 'fechval_mov' => '2026-10-16', 'estado_mov' => 'baja', 'observaciones_mov' => 'Equipo sin reparación posible; se envía a almacén para baja.', 'id_mueble' => 7, 'id_area_origen' => 1, 'id_area_destino' => 7],
        ];
    }
}
