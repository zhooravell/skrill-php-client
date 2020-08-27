<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\ValueObject\Traits\ValueToStringTrait;
use Skrill\Exception\InvalidTransactionIDException;

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
     * @throws InvalidTransactionIDException
     */
    public function __construct($value)
    {
        $value = trim((string)$value);

        if (empty($value)) {
            throw InvalidTransactionIDException::emptyTransactionID();
        }

        $this->value = $value;
    }
}
