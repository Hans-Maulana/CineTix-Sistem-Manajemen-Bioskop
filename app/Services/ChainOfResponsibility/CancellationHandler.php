<?php

namespace App\Services\ChainOfResponsibility;

use App\Models\Booking;

abstract class CancellationHandler
{
    protected ?CancellationHandler $nextHandler = null;

    /**
     * Set next handler dalam chain
     */
    public function setNext(CancellationHandler $handler): CancellationHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * Handle cancellation request dan pass ke next handler jika berhasil
     */
    public function handle(Booking $booking): array
    {
        // Check di handler saat ini
        $check = $this->canCancel($booking);

        if (!$check['allowed']) {
            // Jika tidak diizinkan, return error langsung
            return $check;
        }

        // Jika diizinkan dan ada next handler, pass ke next
        if ($this->nextHandler) {
            return $this->nextHandler->handle($booking);
        }

        // Jika semua check passed, cancellation diizinkan
        return [
            'allowed' => true,
            'message' => 'Cancellation disetujui',
        ];
    }

    /**
     * Abstract method - harus diimplementasikan di subclass
     */
    abstract protected function canCancel(Booking $booking): array;

    /**
     * Helper untuk return rejection
     */
    protected function denied(string $message): array
    {
        return [
            'allowed' => false,
            'message' => $message,
        ];
    }

    /**
     * Helper untuk return allowance
     */
    protected function allowed(): array
    {
        return [
            'allowed' => true,
            'message' => 'OK',
        ];
    }
}
