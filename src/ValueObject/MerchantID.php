<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

/**
 * Value object for merchant id (merchant_id).
 * Unique ID of your Skrill account.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class MerchantID
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
