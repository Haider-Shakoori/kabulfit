<?php

namespace App\Support\Money;

use InvalidArgumentException;

final class MinorMoney
{
    public static function fromDecimalString(string $value): int
    {
        if (! preg_match('/^\d{1,10}(?:\.\d{1,2})?$/', $value)) {
            throw new InvalidArgumentException('Invalid decimal money value.');
        }

        [$major, $minor] = array_pad(explode('.', $value, 2), 2, '0');

        return ((int) $major * 100) + (int) str_pad($minor, 2, '0');
    }
}
