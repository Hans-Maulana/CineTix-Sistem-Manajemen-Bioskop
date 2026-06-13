<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->load([
            'ticketBookings.seat',
            'ticketBookings.schedule.film',
            'ticketBookings.schedule.studio',
            'promo',
        ]);
    }

    public function envelope(): Envelope
    {
        $filmTitle = $this->booking->ticketBookings->first()?->schedule?->film?->title ?? 'Film';

        return new Envelope(
            subject: "Tiket CineTix — {$filmTitle}",
        );
    }

    public function content(): Content
    {
        $ticketUrl = $this->booking->isGuest()
            ? route('booking.guest-ticket', [
                'booking' => $this->booking->id,
                'token' => $this->booking->access_token,
            ])
            : route('booking.tickets');

        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode((string) $this->booking->qr_redeem);

        return new Content(
            view: 'emails.ticket-confirmation',
            with: [
                'ticketUrl' => $ticketUrl,
                'recipientEmail' => $this->booking->customerEmail(),
                'qrUrl' => $qrUrl,
            ],
        );
    }
}
