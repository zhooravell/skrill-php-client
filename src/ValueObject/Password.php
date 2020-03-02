<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidPasswordException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for Skrill API/MQI password.
 *
 * - At least 8 characters long
 * - At least 1 alphabetic character (A-Z)
 * - At least 1 non-alphabetic character (0-9, ., +, etc.)
 * - Cannot be the same as your email address
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class Password
{
    use ValueToStringTrait;

    public const MIN_LENGTH = 8;

    /**
     * @param string $value
     *
     * @throws InvalidPasswordException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (strlen($value) < self::MIN_LENGTH) {
            throw InvalidPasswordException::invalidMinLength();
        }

        if (!preg_match('/\pL/u', $value)) {
            throw InvalidPasswordException::missingLetters();
        }

        if (!preg_match('/\pN/u', $value)) {
            throw InvalidPasswordException::missingNumbers();
        }

        $this->value = $value;
    }
}
