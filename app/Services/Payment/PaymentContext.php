<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentContext
{
    /**
     * Registry of available payment strategies.
     */
    private static array $strategies = [
        'qris' => QrisPaymentStrategy::class,
        'virtual_account' => VirtualAccountPaymentStrategy::class,
    ];

    /**
     * Resolve a payment strategy by method name.
     *
     * @throws InvalidArgumentException
     */
    public static function resolve(string $method): PaymentStrategyInterface
    {
        if (!isset(self::$strategies[$method])) {
            throw new InvalidArgumentException("Metode pembayaran '{$method}' tidak didukung.");
        }

        return new self::$strategies[$method]();
    }

    /**
     * Get all available payment methods.
     */
    public static function availableMethods(): array
    {
        return [
            'qris' => [
                'key' => 'qris',
                'label' => 'QRIS',
                'icon' => '📱',
                'description' => 'Bayar dengan scan QR menggunakan e-wallet atau mobile banking',
            ],
            'virtual_account' => [
                'key' => 'virtual_account',
                'label' => 'Virtual Account',
                'icon' => '🏦',
                'description' => 'Bayar via transfer ke nomor Virtual Account CineTix',
            ],
        ];
    }
}
