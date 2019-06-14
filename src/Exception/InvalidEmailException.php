<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidEmailException.
 */
final class InvalidEmailException extends Exception implements SkrillException
{
    /**
     * @return InvalidEmailException
     */
    public static function invalidEmail(): self
    {
        return new self('Email is not a valid email address.');
    }
}
