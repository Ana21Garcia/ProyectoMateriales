<?php

namespace App\Enums;

/**
 * Estados posibles de un movimiento (columna Estado_mov).
 */
enum EstadoMovimiento: string
{
    case EnCurso = 'en_curso';
    case Completada = 'completada';
    case Rechazada = 'rechazada';
    case Baja = 'baja';

    public function label(): string
    {
        return match ($this) {
            self::EnCurso => 'En curso',
            self::Completada => 'Completada',
            self::Rechazada => 'Rechazada',
            self::Baja => 'Baja',
        };
    }

    /**
     * Clases de Tailwind para la etiqueta del estado.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::EnCurso => 'bg-institucional/10 text-institucional ring-institucional/30',
            self::Completada => 'bg-lima/30 text-oscuro ring-lima',
            self::Rechazada => 'bg-guinda/10 text-guinda ring-guinda/30',
            self::Baja => 'bg-vino/10 text-vino ring-vino/30',
        };
    }

    /**
     * Un movimiento en estado final ya no se puede editar ni cambiar de estado.
     */
    public function esFinal(): bool
    {
        return $this !== self::EnCurso;
    }
}
