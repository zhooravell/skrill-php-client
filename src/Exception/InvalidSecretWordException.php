<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;
use Skrill\ValueObject\SecretWord;

/**
 * Class InvalidSecretWordException.
 */
final class InvalidSecretWordException extends Exception implements SkrillException
{
    /**
     * @return InvalidSecretWordException
     */
    public static function emptySecretWord(): self
    {
        return new self('Skrill secret word should not be blank.');
    }

    /**
     * @return InvalidSecretWordException
     */
    public static function invalidMinLength(): self
    {
        return new self(sprintf('The length of Skrill Secret Word is too short. It should have %d characters or more.',
            SecretWord::MIN_LENGTH));
    }

    /**
     * @return InvalidSecretWordException
     */
    public static function missingLetters(): self
    {
        return new self('Skrill Secret Word must include at least one letter.');
    }

    /**
     * @return InvalidSecretWordException
     */
    public static function missingNonAlphabetic(): self
    {
        return new self('Skrill Secret Word must include at least one non-alphabetic character.');
    }
}
