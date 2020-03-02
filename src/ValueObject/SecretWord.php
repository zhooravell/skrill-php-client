<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidSecretWordException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for Skrill secret word.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class SecretWord
{
    use ValueToStringTrait;

    public const MAX_LENGTH = 10;

    /**
     * @param string $value
     *
     * @throws InvalidSecretWordException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw InvalidSecretWordException::emptySecretWord();
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw InvalidSecretWordException::invalidMaxLength();
        }

        if (preg_match('/[^a-zA-Z0-9\s]/', $value)) {
            throw InvalidSecretWordException::specialCharacters();
        }

        $this->value = $value;
    }
}
