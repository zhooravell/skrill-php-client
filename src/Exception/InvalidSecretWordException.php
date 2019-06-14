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
    public static function invalidMaxLength(): self
    {
        return new self(sprintf('The length of Skrill secret word should not exceed %d characters.', SecretWord::MAX_LENGTH));
    }

    /**
     * @return InvalidSecretWordException
     */
    public static function specialCharacters(): self
    {
        return new self('Special characters are not permitted in Skrill secret word.');
    }
}
