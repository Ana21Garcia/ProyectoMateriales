<?php

namespace App\Enums;

/**
 * Tipos de movimiento (columna Tipo_mov).
 */
enum TipoMovimiento: string
{
    case Traslado = 'traslado';
    case Prestamo = 'prestamo';
    case Mantenimiento = 'mantenimiento';
    case Reasignacion = 'reasignacion';

    public function label(): string
    {
        return match ($this) {
            self::Traslado => 'Traslado',
            self::Prestamo => 'Préstamo',
            self::Mantenimiento => 'Mantenimiento',
            self::Reasignacion => 'Reasignación',
        };
    }
}
