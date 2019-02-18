<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Skrill\ValueObject\Password;

/**
 * Class InvalidPasswordException.
 */
final class InvalidPasswordException extends \Exception implements SkrillException
{
    /**
     * @return InvalidPasswordException
     */
    public static function invalidMinLength()
    {
        return new self(sprintf('Skrill API/MQI password is too short. It should have %d characters or more.', Password::MIN_LENGTH));
    }

    /**
     * @return InvalidPasswordException
     */
    public static function missingLetters()
    {
        return new self('Skrill API/MQI password must include at least one letter.');
    }

    /**
     * @return InvalidPasswordException
     */
    public static function missingNumbers()
    {
        return new self('Skrill API/MQI password must include at least one number.');
    }
}
