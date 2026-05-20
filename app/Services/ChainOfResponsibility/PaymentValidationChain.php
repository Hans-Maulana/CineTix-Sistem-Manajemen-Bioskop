<?php

namespace App\Services\ChainOfResponsibility;

use App\Services\ChainOfResponsibility\PaymentValidation\PaymentPendingValidationHandler;
use App\Services\ChainOfResponsibility\PaymentValidation\PaymentTimeoutValidationHandler;
use App\Services\ChainOfResponsibility\PaymentValidation\PaymentStatusValidationHandler;
use App\Services\ChainOfResponsibility\PaymentValidation\BookingStatusValidationHandler;

class PaymentValidationChain
{
    /**
     * Build payment validation chain
     * Order: Pending → Timeout → Status → Booking Status
     */
    public static function build(): PaymentValidationHandler
    {
        $handler1 = new PaymentPendingValidationHandler();
        $handler2 = new PaymentTimeoutValidationHandler();
        $handler3 = new PaymentStatusValidationHandler();
        $handler4 = new BookingStatusValidationHandler();

        $handler1->setNext($handler2)
            ->setNext($handler3)
            ->setNext($handler4);

        return $handler1;
    }
}
