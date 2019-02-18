<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidEmailException.
 */
final class InvalidEmailException extends \Exception implements SkrillException
{
    /**
     * @return InvalidEmailException
     */
    public static function invalidEmail()
    {
        return new self('Email is not a valid email address.');
    }
}
