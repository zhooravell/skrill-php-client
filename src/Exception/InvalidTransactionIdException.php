<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidTransactionIdException.
 */
final class InvalidTransactionIdException extends \Exception implements SkrillException
{
    /**
     * @return InvalidTransactionIdException
     */
    public static function emptyTransactionId()
    {
        return new self('Skrill transaction ID should not be blank.');
    }
}
