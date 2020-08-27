<?php

declare(strict_types=1);

namespace Skrill\Request;

use Skrill\ValueObject\Email;
use Skrill\ValueObject\Customer;
use Skrill\ValueObject\DateOfBirth;
use Skrill\ValueObject\Address;
use Skrill\ValueObject\CustomerId;
use Skrill\Request\Traits\GetPayloadTrait;
use Skrill\ValueObject\MerchantID;

/**
 * Class CustomerVerificationRequest.
 */
final class CustomerVerificationRequest
{
    use GetPayloadTrait;

    /**
     * CustomerVerificationRequest constructor.
     * @param Email $email
     * @param Customer $customer
     * @param DateOfBirth $dateOfBirth
     * @param Address $address
     * @param MerchantID $merchantId
     * @param CustomerId|null $customerId
     */
    public function __construct(
        Email $email,
        Customer $customer,
        DateOfBirth $dateOfBirth,
        Address $address,
        MerchantID $merchantId,
        CustomerId $customerId = null
    )
    {
        $this->payload = [
            'email' => (string)$email,
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'dateOfBirth' => (string)$dateOfBirth,
            'postCode' => $address->getPostCode(),
            'country' => $address->getCountry(),
            'houseNumber' => $address->getHouseNumber(),
            'merchantId' => $merchantId->getValue(),
        ];
        if ($customerId !== null) {
            $this->payload['customerId'] = $customerId;
        }
    }
}
