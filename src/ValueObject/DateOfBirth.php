<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Class DateOfBirth
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
final class DateOfBirth
{
    use ValueToStringTrait;

    /**
     * Date of birth of the customer. The format is YYYYMMDD.
     * Only numeric values are accepted t e.g. 1st December 1970 = 19701201
     * @param string $value
     */
    public function __construct(string $value = null)
    {
        $this->value = '';
        if ($value && $this->validateDateBefore(trim($value), '-18 YEARS')) {
            $this->value = (new \DateTime(trim($value)))->format('Ymd');
        }
    }

    /**
     * Validate the date is before a given date
     * @param  string  $vtime
     * @param  string  $ptime
     * @return bool
     */
    protected function validateDateBefore($vtime, $ptime)
    {
        $vtime = ($vtime instanceof \DateTime) ? $vtime->getTimestamp() : strtotime($vtime);
        $ptime = ($ptime instanceof \DateTime) ? $ptime->getTimestamp() : strtotime($ptime);

        return $vtime < $ptime;
    }}