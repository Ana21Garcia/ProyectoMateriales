<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

/**
 * Catálogos de apoyo: edificios, áreas, usuarios y muebles.
 *
 * Los muebles se guardan en sesión porque los movimientos cambian su área
 * y su estado. Al conectar la base de datos estos métodos se sustituyen por
 * los modelos Edificio, Area, Usuario y Mueble.
 */
class CatalogoRepository extends SessionRepository
{
    protected function key(): string
    {
        return 'demo.muebles';
    }

    protected function primaryKey(): string
    {
        return 'id_mueble';
    }

    /**
     * @return Collection<int, Fluent>
     */
    public function edificios(): Collection
    {
        return collect([
            ['id_edificio' => 1, 'nombre_edificio' => 'Edificio A', 'descripcion_edificio' => 'Dirección y administración', 'planta' => 'Alta', 'estatus' => true],
            ['id_edificio' => 2, 'nombre_edificio' => 'Edificio B', 'descripcion_edificio' => 'Aulas y laboratorios', 'planta' => 'Baja', 'estatus' => true],
            ['id_edificio' => 3, 'nombre_edificio' => 'Edificio C', 'descripcion_edificio' => 'Centro de cómputo', 'planta' => 'Baja', 'estatus' => true],
        ])->keyBy('id_edificio')->map(fn (array $edificio) => new Fluent($edificio));
    }

    /**
     * @return Collection<int, Fluent>
     */
    public function areas(): Collection
    {
        $edificios = $this->edificios();

        return collect([
            ['id_area' => 1, 'nombre_area' => 'Recursos Materiales', 'descripcion_area' => 'Control de bienes e inventario', 'estatus' => true, 'id_edificio' => 1],
            ['id_area' => 2, 'nombre_area' => 'Dirección General', 'descripcion_area' => 'Oficinas de dirección', 'estatus' => true, 'id_edificio' => 1],
            ['id_area' => 3, 'nombre_area' => 'Servicios Escolares', 'descripcion_area' => 'Atención a estudiantes', 'estatus' => true, 'id_edificio' => 1],
            ['id_area' => 4, 'nombre_area' => 'Laboratorio de Química', 'descripcion_area' => 'Prácticas de laboratorio', 'estatus' => true, 'id_edificio' => 2],
            ['id_area' => 5, 'nombre_area' => 'Aula B-12', 'descripcion_area' => 'Aula de clases', 'estatus' => true, 'id_edificio' => 2],
            ['id_area' => 6, 'nombre_area' => 'Centro de Cómputo', 'descripcion_area' => 'Equipo de cómputo institucional', 'estatus' => true, 'id_edificio' => 3],
            ['id_area' => 7, 'nombre_area' => 'Almacén General', 'descripcion_area' => 'Resguardo temporal de bienes', 'estatus' => true, 'id_edificio' => 3],
        ])->keyBy('id_area')->map(fn (array $area) => new Fluent([
            ...$area,
            'edificio' => $edificios->get($area['id_edificio']),
        ]));
    }

    public function area(?int $id): ?Fluent
    {
        return $id ? $this->areas()->get($id) : null;
    }

    /**
     * @return Collection<int, Fluent>
     */
    public function usuarios(): Collection
    {
        return collect([
            ['id_usuario' => 1, 'nombre_usuario' => 'María', 'apaterno_usuario' => 'López', 'amaterno_responsable' => 'Hernández', 'correo_usuario' => 'maria.lopez@itssmt.edu.mx', 'area_adscripcion' => 'Recursos Materiales', 'estatus' => true],
            ['id_usuario' => 2, 'nombre_usuario' => 'Jorge', 'apaterno_usuario' => 'Ramírez', 'amaterno_responsable' => 'Cruz', 'correo_usuario' => 'jorge.ramirez@itssmt.edu.mx', 'area_adscripcion' => 'Centro de Cómputo', 'estatus' => true],
            ['id_usuario' => 3, 'nombre_usuario' => 'Laura', 'apaterno_usuario' => 'Sánchez', 'amaterno_responsable' => 'Ortiz', 'correo_usuario' => 'laura.sanchez@itssmt.edu.mx', 'area_adscripcion' => 'Laboratorio de Química', 'estatus' => true],
            ['id_usuario' => 4, 'nombre_usuario' => 'Ricardo', 'apaterno_usuario' => 'Torres', 'amaterno_responsable' => 'Flores', 'correo_usuario' => 'ricardo.torres@itssmt.edu.mx', 'area_adscripcion' => 'Dirección General', 'estatus' => true],
        ])->keyBy('id_usuario')->map(fn (array $usuario) => new Fluent([
            ...$usuario,
            'nombre_completo' => "{$usuario['nombre_usuario']} {$usuario['apaterno_usuario']} {$usuario['amaterno_responsable']}",
        ]));
    }

    public function usuario(?int $id): ?Fluent
    {
        return $id ? $this->usuarios()->get($id) : null;
    }

    /**
     * @return Collection<int, Fluent>
     */
    public function muebles(): Collection
    {
        return collect($this->rows())->map(fn (array $mueble) => $this->hydrateMueble($mueble));
    }

    public function mueble(?int $id): ?Fluent
    {
        $row = $id ? $this->findRow($id) : null;

        return $row ? $this->hydrateMueble($row) : null;
    }

    /**
     * Cambia el área donde se encuentra un mueble (al completar un traslado).
     */
    public function moverMueble(int $idMueble, int $idArea): void
    {
        $this->updateRow($idMueble, ['id_area' => $idArea]);
    }

    public function cambiarEstadoMueble(int $idMueble, string $estado): void
    {
        $this->updateRow($idMueble, ['estado_mueble' => $estado]);
    }

    private function hydrateMueble(array $mueble): Fluent
    {
        return new Fluent([
            ...$mueble,
            'area' => $this->area($mueble['id_area']),
            'usuario' => $this->usuario($mueble['id_usuario']),
        ]);
    }

    protected function seed(): array
    {
        return [
            ['id_mueble' => 1, 'nombre_mueble' => 'Escritorio ejecutivo', 'descripcion_mueble' => 'Escritorio de madera con 3 cajones', 'modelo_mueble' => 'EJ-1200', 'codigo_mueble' => 'ITS-MOB-0001', 'categoria' => 'Mobiliario', 'fechreg_mueble' => '2024-02-15', 'marca_mueble' => 'Offiho', 'estado_mueble' => 'Bueno', 'descri_ubicacion' => 'Oficina del jefe de departamento', 'id_area' => 1, 'id_usuario' => 1],
            ['id_mueble' => 2, 'nombre_mueble' => 'Silla ergonómica', 'descripcion_mueble' => 'Silla con respaldo de malla y ruedas', 'modelo_mueble' => 'ERGO-50', 'codigo_mueble' => 'ITS-MOB-0002', 'categoria' => 'Mobiliario', 'fechreg_mueble' => '2024-02-15', 'marca_mueble' => 'Steelcase', 'estado_mueble' => 'Bueno', 'descri_ubicacion' => 'Junto a recepción', 'id_area' => 2, 'id_usuario' => 4],
            ['id_mueble' => 3, 'nombre_mueble' => 'Computadora de escritorio', 'descripcion_mueble' => 'Core i5, 16 GB RAM, monitor 24"', 'modelo_mueble' => 'OptiPlex 7010', 'codigo_mueble' => 'ITS-COM-0103', 'categoria' => 'Equipo de cómputo', 'fechreg_mueble' => '2024-08-01', 'marca_mueble' => 'Dell', 'estado_mueble' => 'Bueno', 'descri_ubicacion' => 'Fila 2, lugar 5', 'id_area' => 6, 'id_usuario' => 2],
            ['id_mueble' => 4, 'nombre_mueble' => 'Proyector', 'descripcion_mueble' => 'Proyector 3LCD 3600 lúmenes', 'modelo_mueble' => 'PowerLite X49', 'codigo_mueble' => 'ITS-AV-0021', 'categoria' => 'Audiovisual', 'fechreg_mueble' => '2023-09-10', 'marca_mueble' => 'Epson', 'estado_mueble' => 'Regular', 'descri_ubicacion' => 'Montado en techo', 'id_area' => 5, 'id_usuario' => 3],
            ['id_mueble' => 5, 'nombre_mueble' => 'Archivero metálico', 'descripcion_mueble' => 'Archivero de 4 gavetas tamaño oficio', 'modelo_mueble' => 'AR-4G', 'codigo_mueble' => 'ITS-MOB-0040', 'categoria' => 'Mobiliario', 'fechreg_mueble' => '2022-05-20', 'marca_mueble' => 'Lyon', 'estado_mueble' => 'Regular', 'descri_ubicacion' => 'Pasillo de archivo', 'id_area' => 3, 'id_usuario' => 1],
            ['id_mueble' => 6, 'nombre_mueble' => 'Campana de extracción', 'descripcion_mueble' => 'Campana de extracción de gases 1.5 m', 'modelo_mueble' => 'CE-150', 'codigo_mueble' => 'ITS-LAB-0007', 'categoria' => 'Equipo de laboratorio', 'fechreg_mueble' => '2023-01-12', 'marca_mueble' => 'Labconco', 'estado_mueble' => 'Bueno', 'descri_ubicacion' => 'Muro norte', 'id_area' => 4, 'id_usuario' => 3],
            ['id_mueble' => 7, 'nombre_mueble' => 'Impresora multifuncional', 'descripcion_mueble' => 'Impresora láser con escáner', 'modelo_mueble' => 'M428fdw', 'codigo_mueble' => 'ITS-COM-0150', 'categoria' => 'Equipo de cómputo', 'fechreg_mueble' => '2021-11-03', 'marca_mueble' => 'HP', 'estado_mueble' => 'Baja', 'descri_ubicacion' => 'Estante de equipo en revisión', 'id_area' => 7, 'id_usuario' => 1],
        ];
    }
}
