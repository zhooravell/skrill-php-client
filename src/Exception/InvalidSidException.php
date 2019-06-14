<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidSidException.
 */
final class InvalidSidException extends Exception implements SkrillException
{
    /**
     * @return InvalidSidException
     */
    public static function emptySid(): self
    {
        return new self('Skrill sid should not be blank.');
    }
}
