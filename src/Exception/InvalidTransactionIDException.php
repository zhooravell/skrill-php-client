<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidTransactionIDException.
 */
final class InvalidTransactionIDException extends Exception implements SkrillException
{
    /**
     * @return InvalidTransactionIDException
     */
    public static function emptyTransactionID(): self
    {
        return new self('Skrill transaction ID should not be blank.');
    }
}
