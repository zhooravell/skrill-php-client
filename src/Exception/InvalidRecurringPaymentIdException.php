<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidRecurringPaymentIdException.
 */
final class InvalidRecurringPaymentIdException extends \Exception implements SkrillException
{
    /**
     * @return InvalidRecurringPaymentIdException
     */
    public static function emptyTransactionId()
    {
        return new self('Skrill recurring payment ID should not be blank.');
    }
}
