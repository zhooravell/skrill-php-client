<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidRecurringPaymentIDException.
 */
final class InvalidRecurringPaymentIDException extends \Exception implements SkrillException
{
    /**
     * @return InvalidRecurringPaymentIDException
     */
    public static function emptyTransactionID()
    {
        return new self('Skrill recurring payment ID should not be blank.');
    }
}
