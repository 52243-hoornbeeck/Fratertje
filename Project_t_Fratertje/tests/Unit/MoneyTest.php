<?php
namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_money_uses_integer_cents(): void
    {
        $this->assertSame(3600, Money::cents('36'));
        $this->assertSame(1850, Money::cents('18.5'));
        $this->assertSame(1801, Money::cents('18.01'));
        $this->assertSame(0, Money::cents('0.00'));
        $this->assertSame('€ 1.234,56', Money::euros(123456));
    }
}
