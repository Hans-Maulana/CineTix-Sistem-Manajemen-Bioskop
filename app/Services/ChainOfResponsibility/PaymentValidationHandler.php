<?php

namespace App\Services\ChainOfResponsibility;

use App\Models\Booking;
use App\Models\Payment;

abstract class PaymentValidationHandler
{
    protected ?PaymentValidationHandler $nextHandler = null;

    /**
     * Set next handler dalam chain
     */
    public function setNext(PaymentValidationHandler $handler): PaymentValidationHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * Handle request dan pass ke next handler jika berhasil
     */
    public function handle(Booking $booking, Payment $payment): array
    {
        // Validasi di handler saat ini
        $validation = $this->validate($booking, $payment);

        if (!$validation['valid']) {
            // Jika tidak valid, return error langsung
            return $validation;
        }

        // Jika valid dan ada next handler, pass ke next
        if ($this->nextHandler) {
            return $this->nextHandler->handle($booking, $payment);
        }

        // Jika semua valid, return success
        return [
            'valid' => true,
            'message' => 'Semua validasi berhasil',
        ];
    }

    /**
     * Abstract method - harus diimplementasikan di subclass
     */
    abstract protected function validate(Booking $booking, Payment $payment): array;

    /**
     * Helper untuk return error
     */
    protected function error(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
        ];
    }

    /**
     * Helper untuk return success
     */
    protected function success(): array
    {
        return [
            'valid' => true,
            'message' => 'OK',
        ];
    }
}
