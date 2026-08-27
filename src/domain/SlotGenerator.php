<?php

declare(strict_types=1);

namespace App\Domain;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

final class SlotGenerator
{
    /**
     * Genera una lista de franjas horarias entre dos horas.
     *
     * @param string $start Hora de inicio (formato HH:MM o HH:MM:SS)
     * @param string $end Hora de fin (formato HH:MM o HH:MM:SS)
     * @param int $minutes Duración de cada franja en minutos
     * @return list<string> Lista de horas en formato HH:MM:SS
     * @throws InvalidArgumentException Si los parámetros no son válidos
     */
    public function generate(string $start = '08:00', string $end = '12:00', int $minutes = 20): array
    {
        if ($minutes <= 0 || $minutes > 720) {
            throw new InvalidArgumentException('La duración debe estar entre 1 y 720 minutos.');
        }

        $current = $this->parseTime($start);
        $limit = $this->parseTime($end);

        if ($current >= $limit) {
            throw new InvalidArgumentException('La hora final debe ser posterior a la hora inicial.');
        }

        $interval = new \DateInterval('PT' . $minutes . 'M');
        $slots = [];

        while ($current < $limit) {
            $next = $current->add($interval);
            if ($next > $limit) {
                break;
            }
            $slots[] = $current->format('H:i:s');
            $current = $next;
        }

        return $slots;
    }

    /**
     * Convierte un string de hora en un objeto DateTimeImmutable.
     *
     * @param string $time Hora en formato HH:MM o HH:MM:SS
     * @return DateTimeImmutable
     * @throws InvalidArgumentException Si el formato no es válido
     */
    private function parseTime(string $time): DateTimeImmutable
    {
        // Normaliza: si tiene 5 caracteres (HH:MM), agrega :00
        $normalized = strlen($time) === 5 ? $time . ':00' : $time;
        $parsed = DateTimeImmutable::createFromFormat('H:i:s', $normalized);

        if ($parsed === false || $parsed->format('H:i:s') !== $normalized) {
            throw new InvalidArgumentException("La hora '{$time}' no tiene un formato válido.");
        }

        return $parsed;
    }
}