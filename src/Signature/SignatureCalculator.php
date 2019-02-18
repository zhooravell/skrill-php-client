<?php

declare(strict_types=1);

namespace Skrill\Signature;

use Money\Money;
use Skrill\ValueObject\Signature;
use Skrill\ValueObject\TransactionID;
use Skrill\Exception\InvalidSignatureException;

/**
 * Interface Skrill signature calculator.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
interface SignatureCalculator
{
    /**
     * @param TransactionID $transactionId
     * @param Money         $amount
     * @param int           $status
     *
     * @return Signature
     *
     * @throws InvalidSignatureException
     */
    public function calculate(TransactionID $transactionId, Money $amount, int $status);
}
