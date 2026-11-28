<?php

namespace App\Enums;

/**
 * Estados posibles de un resguardo (columna Estado_Resguardo).
 */
enum EstadoResguardo: string
{
    case Activo = 'activo';
    case Liberado = 'liberado';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Liberado => 'Liberado',
            self::Cancelado => 'Cancelado',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Activo => 'bg-lima/30 text-oscuro ring-lima',
            self::Liberado => 'bg-dorado/15 text-dorado ring-dorado/40',
            self::Cancelado => 'bg-guinda/10 text-guinda ring-guinda/30',
        };
    }
}
