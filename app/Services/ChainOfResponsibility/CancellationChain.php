<?php

namespace App\Services\ChainOfResponsibility;

use App\Services\ChainOfResponsibility\Cancellation\CancellationStatusHandler;
use App\Services\ChainOfResponsibility\Cancellation\LockedSeatsHandler;
use App\Services\ChainOfResponsibility\Cancellation\PaymentRefundHandler;

class CancellationChain
{
    /**
     * Build cancellation chain
     * Order: Status Check → Locked Seats Check → Payment Refund Check
     */
    public static function build(): CancellationHandler
    {
        $handler1 = new CancellationStatusHandler();
        $handler2 = new LockedSeatsHandler();
        $handler3 = new PaymentRefundHandler();

        $handler1->setNext($handler2)
            ->setNext($handler3);

        return $handler1;
    }
}
