<?php

namespace Tests\Unit;

use App\Support\Measurements\MeasurementConverter;
use PHPUnit\Framework\TestCase;

class MeasurementConverterTest extends TestCase
{
    public function test_inches_and_centimetres_convert_with_two_decimal_precision(): void
    {
        $this->assertSame('25.40', MeasurementConverter::toCm(10, 'in'));
        $this->assertSame('10.00', MeasurementConverter::fromCm('25.40', 'in'));
        $this->assertSame('42.35', MeasurementConverter::toCm('42.35', 'cm'));
    }
}
