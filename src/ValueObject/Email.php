<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidEmailException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for email.
 */
final class Email
{
    use ValueToStringTrait;

    /**
     * @param string $value
     *
     * @throws InvalidEmailException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (false == filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::invalidEmail();
        }

        $this->value = $value;
    }
}
