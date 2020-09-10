<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

/**
 * Class Customer
 *
 * Value object for Customer Verification Service.
 * The customer verification service is used to check if one of your customers, identified by an email
 * address or customer ID, is registered with Skrill (i.e. the customer already has an active Skrill Digital
 * Wallet account).You can also verify information that you hold about the customer against Skrill’s
 * registration records.
 *
 * https://www.skrill.com/fileadmin/content/pdf/Skrill_Customer_Verification_Service_Guide_v1.1__1_.pdf
 *
 */
final class Customer
{
    /** @var string */
    private $lastName  = '';

    /** @var string */
    private $firstName = '';

    /**
     * CustomerName constructor.
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(string $firstName = '', string $lastName = '')
    {
        $firstName = trim($firstName);
        $lastName  = trim($lastName);

        if ($this->validateAlpha($firstName)) {
            $this->firstName = $firstName;
        }

        if ($this->validateAlpha($lastName)) {
            $this->lastName  = $lastName;
        }
    }

    /**
     * Customer’s first name.
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Customer’s last name
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Validate that a field contains only alphabetic characters
     * @param  string $value
     * @return bool
     */
    private function validateAlpha($value): bool
    {
        return (bool)preg_match('/^([a-z])+$/i', $value);
    }
}
