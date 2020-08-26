<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

/**
 * Value object for customer id (customerId).
 * Unique ID of your Skrill account.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Customer_Verification_Service_Guide_v1.1__1_.pdf
 */
final class CustomerId
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
