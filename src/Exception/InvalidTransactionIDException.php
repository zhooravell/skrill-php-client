<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidTransactionIDException.
 */
final class InvalidTransactionIDException extends \Exception implements SkrillException
{
    /**
     * @return InvalidTransactionIDException
     */
    public static function emptyTransactionID()
    {
        return new self('Skrill transaction ID should not be blank.');
    }
}
