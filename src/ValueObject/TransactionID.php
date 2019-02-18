<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\ValueObject\Traits\ValueToStringTrait;
use Skrill\Exception\InvalidTransactionIdException;

/**
 * Value object for transaction id (transaction_id or mb_transaction_id).
 * A unique reference or identification number.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class TransactionID
{
    use ValueToStringTrait;

    /**
     * @param string|int $value
     *
     * @throws InvalidTransactionIdException
     */
    public function __construct($value)
    {
        $value = trim(strval($value));

        if (empty($value)) {
            throw InvalidTransactionIdException::emptyTransactionId();
        }

        $this->value = $value;
    }
}
