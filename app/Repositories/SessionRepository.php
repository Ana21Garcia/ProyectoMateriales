<?php

namespace App\Repositories;

/**
 * Almacén temporal que guarda los registros en la sesión del usuario.
 *
 * Sirve para probar las vistas sin base de datos. Cuando se conecte la base,
 * cada repositorio hijo se reemplaza por consultas Eloquent manteniendo los
 * mismos métodos públicos, y los controladores no cambian.
 */
abstract class SessionRepository
{
    /**
     * Clave de sesión donde se guardan los registros.
     */
    abstract protected function key(): string;

    /**
     * Nombre de la columna que funciona como llave primaria.
     */
    abstract protected function primaryKey(): string;

    /**
     * Datos de ejemplo con los que inicia el almacén.
     *
     * @return list<array<string, mixed>>
     */
    abstract protected function seed(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function rows(): array
    {
        if (! session()->has($this->key())) {
            $this->save(collect($this->seed())->keyBy($this->primaryKey())->all());
        }

        return session($this->key());
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function save(array $rows): void
    {
        session()->put($this->key(), $rows);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findRow(int $id): ?array
    {
        return $this->rows()[$id] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function findRowOrFail(int $id): array
    {
        return $this->findRow($id) ?? abort(404);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function insertRow(array $data): array
    {
        $rows = $this->rows();
        $id = empty($rows) ? 1 : max(array_keys($rows)) + 1;

        $row = [$this->primaryKey() => $id, ...$data];
        $rows[$id] = $row;
        $this->save($rows);

        return $row;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function updateRow(int $id, array $data): array
    {
        $rows = $this->rows();
        $row = [...$this->findRowOrFail($id), ...$data];
        $rows[$id] = $row;
        $this->save($rows);

        return $row;
    }

    public function delete(int $id): void
    {
        $rows = $this->rows();
        unset($rows[$id]);
        $this->save($rows);
    }
}
