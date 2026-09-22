<?php

namespace App\Support\Measurements;

use InvalidArgumentException;

final class MeasurementConverter
{
    public static function toCm(string|float|int $value, string $unit): string
    {
        $numeric = (float) $value;

        return match ($unit) {
            'cm' => number_format($numeric, 2, '.', ''),
            'in' => number_format($numeric * 2.54, 2, '.', ''),
            default => throw new InvalidArgumentException('Unsupported measurement unit.'),
        };
    }

    public static function fromCm(string|float|int $value, string $unit): string
    {
        $numeric = (float) $value;

        return match ($unit) {
            'cm' => number_format($numeric, 2, '.', ''),
            'in' => number_format($numeric / 2.54, 2, '.', ''),
            default => throw new InvalidArgumentException('Unsupported measurement unit.'),
        };
    }
}
