<?php

declare(strict_types=1);

namespace Skrill\Request;

use Money\Money;
use Skrill\ValueObject\Url;
use Skrill\ValueObject\TransactionID;
use Skrill\Request\Traits\GetPayloadTrait;
use Skrill\ValueObject\RecurringPaymentID;
use Skrill\Request\Traits\AmountFormatterTrait;

/**
 * Class OnDemandRequest.
 */
final class OnDemandRequest
{
    use GetPayloadTrait;
    use AmountFormatterTrait;

    /**
     * @param RecurringPaymentID $paymentId
     * @param TransactionID      $transactionId
     * @param Money              $amount
     */
    public function __construct(RecurringPaymentID $paymentId, TransactionID $transactionId, Money $amount)
    {
        $this->payload = [
            'frn_trn_id' => strval($transactionId),
            'rec_payment_id' => strval($paymentId),
            'currency' => strval($amount->getCurrency()),
            'amount' => $this->formatToFloat($amount),
        ];
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function setStatusUrl(Url $url)
    {
        $this->payload['ondemand_status_url'] = strval($url);

        return $this;
    }
}
