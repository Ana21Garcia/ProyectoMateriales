<?php

namespace App\Repositories;

use App\Enums\EstadoResguardo;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

/**
 * Resguardos de muebles a cargo de un usuario (tabla Resguardo).
 *
 * Columnas según el diagrama: folio_resguardo, fech_resguardo,
 * estado_resguardo, id_mueble, id_usuario e id_ubi (área de ubicación).
 */
class ResguardoRepository extends SessionRepository
{
    public function __construct(private CatalogoRepository $catalogo) {}

    protected function key(): string
    {
        return 'demo.resguardos';
    }

    protected function primaryKey(): string
    {
        return 'id_resguardo';
    }

    /**
     * @return Collection<int, Fluent>
     */
    public function all(?EstadoResguardo $estado = null, ?string $buscar = null): Collection
    {
        return collect($this->rows())
            ->map(fn (array $row) => $this->hydrate($row))
            ->when($estado, fn (Collection $items) => $items->filter(fn (Fluent $res) => $res->estado_resguardo === $estado))
            ->when($buscar, fn (Collection $items) => $items->filter(fn (Fluent $res) => str_contains(
                mb_strtolower("{$res->folio_resguardo} {$res->mueble?->nombre_mueble} {$res->mueble?->codigo_mueble} {$res->usuario?->nombre_completo}"),
                mb_strtolower($buscar),
            )))
            ->sortByDesc('fech_resguardo')
            ->values();
    }

    public function findOrFail(int $id): Fluent
    {
        return $this->hydrate($this->findRowOrFail($id));
    }

    /**
     * Un mueble solo puede tener un resguardo activo a la vez.
     */
    public function muebleTieneResguardoActivo(int $idMueble, ?int $exceptoId = null): bool
    {
        return collect($this->rows())->contains(fn (array $row) => $row['id_mueble'] === $idMueble
            && $row['estado_resguardo'] === EstadoResguardo::Activo->value
            && $row['id_resguardo'] !== $exceptoId);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Fluent
    {
        return $this->hydrate($this->insertRow([
            'folio_resguardo' => $this->siguienteFolio(),
            ...$this->normalizar($data),
        ]));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): Fluent
    {
        return $this->hydrate($this->updateRow($id, $this->normalizar($data)));
    }

    /**
     * Genera el folio con formato RES-AAAA-0001.
     */
    public function siguienteFolio(): string
    {
        $anio = now()->year;
        $consecutivo = collect($this->rows())
            ->pluck('folio_resguardo')
            ->filter(fn (string $folio) => str_starts_with($folio, "RES-{$anio}-"))
            ->map(fn (string $folio) => (int) substr($folio, -4))
            ->max() ?? 0;

        return sprintf('RES-%d-%04d', $anio, $consecutivo + 1);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizar(array $data): array
    {
        return [
            'fech_resguardo' => $data['fech_resguardo'],
            'estado_resguardo' => $data['estado_resguardo'],
            'id_mueble' => (int) $data['id_mueble'],
            'id_usuario' => (int) $data['id_usuario'],
            'id_ubi' => (int) $data['id_ubi'],
        ];
    }

    private function hydrate(array $row): Fluent
    {
        return new Fluent([
            ...$row,
            'estado_resguardo' => EstadoResguardo::from($row['estado_resguardo']),
            'mueble' => $this->catalogo->mueble($row['id_mueble']),
            'usuario' => $this->catalogo->usuario($row['id_usuario']),
            'ubicacion' => $this->catalogo->area($row['id_ubi']),
        ]);
    }

    protected function seed(): array
    {
        return [
            ['id_resguardo' => 1, 'folio_resguardo' => 'RES-2026-0001', 'fech_resguardo' => '2026-01-12', 'estado_resguardo' => 'activo', 'id_mueble' => 1, 'id_usuario' => 1, 'id_ubi' => 1],
            ['id_resguardo' => 2, 'folio_resguardo' => 'RES-2026-0002', 'fech_resguardo' => '2026-02-03', 'estado_resguardo' => 'activo', 'id_mueble' => 3, 'id_usuario' => 2, 'id_ubi' => 6],
            ['id_resguardo' => 3, 'folio_resguardo' => 'RES-2026-0003', 'fech_resguardo' => '2026-03-21', 'estado_resguardo' => 'activo', 'id_mueble' => 6, 'id_usuario' => 3, 'id_ubi' => 4],
            ['id_resguardo' => 4, 'folio_resguardo' => 'RES-2026-0004', 'fech_resguardo' => '2026-05-08', 'estado_resguardo' => 'liberado', 'id_mueble' => 2, 'id_usuario' => 1, 'id_ubi' => 1],
            ['id_resguardo' => 5, 'folio_resguardo' => 'RES-2026-0005', 'fech_resguardo' => '2026-06-30', 'estado_resguardo' => 'cancelado', 'id_mueble' => 7, 'id_usuario' => 4, 'id_ubi' => 2],
        ];
    }
}
