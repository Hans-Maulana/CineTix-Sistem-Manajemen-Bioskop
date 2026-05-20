<?php

namespace App\Services\ChainOfResponsibility;

use App\Services\ChainOfResponsibility\BookingApproval\UserAuthorizationHandler;
use App\Services\ChainOfResponsibility\BookingApproval\SeatAvailabilityHandler;
use App\Services\ChainOfResponsibility\BookingApproval\PromoValidationHandler;

class BookingApprovalChain
{
    /**
     * Build booking approval chain
     * Order: Authorization → Seat Availability → Promo Validation
     */
    public static function build(): BookingApprovalHandler
    {
        $handler1 = new UserAuthorizationHandler();
        $handler2 = new SeatAvailabilityHandler();
        $handler3 = new PromoValidationHandler();

        $handler1->setNext($handler2)
            ->setNext($handler3);

        return $handler1;
    }
}
