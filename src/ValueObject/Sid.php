<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidSidException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for sid.
 */
final class Sid
{
    use ValueToStringTrait;

    /**
     * @var \DateTimeImmutable
     */
    private $expirationTillDateTime;

    /**
     * @param string             $value
     * @param \DateTimeImmutable $expirationTillDateTime
     *
     * @throws InvalidSidException
     */
    public function __construct(string $value, \DateTimeImmutable $expirationTillDateTime)
    {
        $value = trim($value);

        if (empty($value)) {
            throw InvalidSidException::emptySid();
        }

        $this->value = $value;
        $this->expirationTillDateTime = $expirationTillDateTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getExpirationTillDateTime()
    {
        return $this->expirationTillDateTime;
    }
}
