<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $reason;

    public function __construct(Booking $booking, string $reason)
    {
        $this->booking = $booking;
        $this->reason  = $reason;
    }

    public function build()
    {
        return $this
            ->subject('CineTix — Pengajuan Refund Anda Ditolak')
            ->view('emails.refund-rejected');
    }
}
