<?php
namespace App\Support;

final class Money
{
    public static function euros(int $cents): string
    {
        return '€ '.number_format($cents / 100, 2, ',', '.');
    }

    /** Parse a validated decimal string without floating-point money arithmetic. */
    public static function cents(string $amount): int
    {
        [$euros, $fraction] = array_pad(explode('.', $amount, 2), 2, '');
        return ((int) $euros * 100) + (int) str_pad($fraction, 2, '0');
    }
}
