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

    public const MIN_LENGTH = 8;

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

        if (strlen($value) < self::MIN_LENGTH) {
            throw InvalidSecretWordException::invalidMinLength();
        }

        if (!preg_match('/\pL/u', $value)) {
            throw InvalidSecretWordException::missingLetters();
        }

        if (!preg_match('/[^a-z]/ui', $value)) {
            throw InvalidSecretWordException::missingNonAlphabetic();
        }

        $this->value = $value;
    }
}
