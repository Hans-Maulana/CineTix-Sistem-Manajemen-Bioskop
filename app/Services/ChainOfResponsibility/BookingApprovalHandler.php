<?php

namespace App\Services\ChainOfResponsibility;

use App\Models\Booking;
use Illuminate\Support\Collection;

abstract class BookingApprovalHandler
{
    protected ?BookingApprovalHandler $nextHandler = null;

    /**
     * Set next handler dalam chain
     */
    public function setNext(BookingApprovalHandler $handler): BookingApprovalHandler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * Handle request dan pass ke next handler jika berhasil
     */
    public function handle(array $bookingData): array
    {
        // Validasi di handler saat ini
        $validation = $this->approve($bookingData);

        if (!$validation['approved']) {
            // Jika tidak disetujui, return error langsung
            return $validation;
        }

        // Gunakan booking_data yang sudah dimodifikasi dari handler ini (jika ada)
        $updatedData = $validation['booking_data'] ?? $bookingData;

        // Jika approved dan ada next handler, pass ke next dengan data yang sudah dimodifikasi
        if ($this->nextHandler) {
            return $this->nextHandler->handle($updatedData);
        }

        // Jika semua disetujui, return success dengan data terakhir yang sudah dimodifikasi
        return [
            'approved' => true,
            'message' => 'Booking disetujui dan siap untuk pembayaran',
            'booking_data' => $updatedData,
        ];
    }

    /**
     * Abstract method - harus diimplementasikan di subclass
     */
    abstract protected function approve(array $bookingData): array;

    /**
     * Helper untuk return rejection
     */
    protected function reject(string $message): array
    {
        return [
            'approved' => false,
            'message' => $message,
        ];
    }

    /**
     * Helper untuk return approval
     */
    protected function approved(): array
    {
        return [
            'approved' => true,
            'message' => 'OK',
        ];
    }
}
