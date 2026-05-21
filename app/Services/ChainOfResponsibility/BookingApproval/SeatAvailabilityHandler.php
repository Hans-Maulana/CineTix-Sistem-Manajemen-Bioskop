<?php

namespace App\Services\ChainOfResponsibility\BookingApproval;

use App\Models\Seat;
use App\Services\ChainOfResponsibility\BookingApprovalHandler;

class SeatAvailabilityHandler extends BookingApprovalHandler
{
    /**
     * Validasi: apakah semua kursi yang dipilih masih tersedia?
     */
    protected function approve(array $bookingData): array
    {
        $seatIds = $bookingData['seat_ids'] ?? [];
        $scheduleId = $bookingData['schedule_id'] ?? null;

        if (empty($seatIds)) {
            return $this->reject('Tidak ada kursi yang dipilih.');
        }

        // Cek apakah semua kursi masih available
        $unavailableSeats = [];
        
        foreach ($seatIds as $seatId) {
            $seat = Seat::find($seatId);
            
            if (!$seat) {
                $unavailableSeats[] = "Kursi tidak ditemukan (ID: $seatId)";
                continue;
            }

            if (!$seat->isAvailable($scheduleId)) {
                $unavailableSeats[] = $seat->seat_code;
            }
        }

        if (!empty($unavailableSeats)) {
            return $this->reject(
                'Kursi berikut tidak tersedia: ' . implode(', ', $unavailableSeats) . 
                '. Silakan pilih kursi lain.'
            );
        }

        // Return dengan booking_data agar data terforward ke handler berikutnya
        return [
            'approved' => true,
            'message' => 'Semua kursi tersedia',
            'booking_data' => $bookingData,
        ];
    }
}
