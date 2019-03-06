<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\ValueObject\Traits\ValueToStringTrait;
use Skrill\Exception\InvalidRecurringPaymentIDException;

/**
 * Value object for Skrill recurring payment id (rec_payment_id).
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Wallet_Checkout_Guide.pdf
 */
final class RecurringPaymentID
{
    use ValueToStringTrait;

    /**
     * @param string $value
     *
     * @throws InvalidRecurringPaymentIDException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw InvalidRecurringPaymentIDException::emptyTransactionID();
        }

        $this->value = $value;
    }
}
