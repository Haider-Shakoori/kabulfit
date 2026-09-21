<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MoneyStorageTest extends TestCase
{
    public function test_money_examples_use_integer_minor_units(): void
    {
        $minor = 650000;

        $this->assertSame(6500, intdiv($minor, 100));
        $this->assertSame(0, $minor % 100);
    }
}
