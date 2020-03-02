<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidCompanyNameException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for company name (recipient_description).
 * A company name to be shown on the Skrill payment page in the logo area if there is no logo_url parameter.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class CompanyName
{
    const MAX_LENGTH = 30;

    use ValueToStringTrait;

    /**
     * @param string $value
     *
     * @throws InvalidCompanyNameException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (strlen($value) > self::MAX_LENGTH) {
            throw InvalidCompanyNameException::invalidMaxLength();
        }

        $this->value = $value;
    }
}
