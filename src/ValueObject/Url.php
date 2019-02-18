<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidUrlException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for url.
 *
 * @see https://en.wikipedia.org/wiki/Uniform_Resource_Identifier
 */
final class Url
{
    use ValueToStringTrait;

    /**
     * @param $value
     *
     * @throws InvalidUrlException
     */
    public function __construct(string $value)
    {
        $value = trim($value);
        $value = filter_var($value, FILTER_SANITIZE_URL);

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw InvalidUrlException::invalidUrl($value);
        }

        $this->value = $value;
    }
}
