<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

/**
 * Class Address
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
final class Address
{
    /**
     * Length three letter codes
     */
    private const ISO_ALPHA_3 = 3;

    /**
     * Length two-letter codes
     */
    private const ISO_ALPHA_2 = 2;

    /**
     * @var string
     */
    private $houseNumber = '';

    /**
     * @var string
     */
    private $postCode = '';

    /**
     * @var string
     */
    private $country = '';

    /**
     * Address constructor.
     * @param string $postCode Customer’s postal code/ZIP Code.
     * @param string $country  Customer’s country.
     * @param string $address  Customer’s address (for example: "221b Baker street")
     */
    public function __construct(string $postCode = '', string $country = '', string $address = '')
    {
        $postCode = trim(preg_replace('/(\s+)/msi', '', $postCode));
        if (!empty($postCode) && $this->validateAlphaNum($postCode)) {
            $this->postCode = $postCode;
        }

        $country = trim($country);
        if (!empty($country) && ($country = $this->validateCountry($country))) {
            $this->country = $country;
        }

        $address = trim($address);
        if (!empty($address) && $this->validateAddress($address)) {
            // Non‐alphanumeric characters such as spaces and commas are not supported and will return NO_MATCH
            preg_match('/\S+(?<!\W)/', trim($address), $match);
            $this->houseNumber = $match[0];
        }
    }

    /**
     * Matches an alphanumeric string separated from the rest of the text
     * by non‐alphanumeric character in line 1 or 2 of the stored customer address.
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    /**
     * Customer’s postal code/ZIP Code. Only alphanumeric values are accepted
     * and ignored white space for matches e.g. CR34JP matches CR3 4JP
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * Customer’s country in the 3-digit ISO_3166‐1_alpha‐3 country code e.g. DEU for Germany.
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Validate that a field contains only alpha-numeric characters, white space or specialized sybmols (.-,).
     * @param  string $value
     * @return bool
     */
    private function validateAddress($value): bool
    {
        return (bool)preg_match("/^([a-z0-9\'\.\-\s\,])+$/i", $value);
    }

    /**
     * Customer’s country
     * @param  string $value
     * @return string|bool
     */
    private function validateCountry($value)
    {
        if (mb_strlen($value) == self::ISO_ALPHA_3) {
            $countries = getSkillSupportsCountries();
            if (array_key_exists($value, $countries)) {
                return $value;
            }
        }

        if (mb_strlen($value) == self::ISO_ALPHA_2) {
            $countries = convertISO();
            if (array_key_exists($value, $countries)) {
                return $countries[$value];
            }
        }

        if ($country = array_search($value, $countries)) {
            return $country;
        }

        return false;
    }

    /**
     * Validate that a field contains only alpha-numeric characters
     * @param  string $value
     * @return bool
     */
    private function validateAlphaNum($value): bool
    {
        return (bool)preg_match('/^([a-z0-9])+$/i', $value);
    }
}
