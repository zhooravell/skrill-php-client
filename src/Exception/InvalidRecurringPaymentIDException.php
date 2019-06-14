<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidRecurringPaymentIDException.
 */
final class InvalidRecurringPaymentIDException extends Exception implements SkrillException
{
    /**
     * @return InvalidRecurringPaymentIDException
     */
    public static function emptyTransactionID(): self
    {
        return new self('Skrill recurring payment ID should not be blank.');
    }
}
