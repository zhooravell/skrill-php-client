<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidSidException.
 */
final class InvalidSidException extends \Exception implements SkrillException
{
    /**
     * @return InvalidSidException
     */
    public static function emptySid()
    {
        return new self('Skrill sid should not be blank.');
    }
}
