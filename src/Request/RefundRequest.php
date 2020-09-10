<?php

declare(strict_types=1);

namespace Skrill\Request;

use Money\Money;
use Skrill\ValueObject\Url;
use Skrill\ValueObject\TransactionID;
use Skrill\Request\Traits\GetPayloadTrait;
use Skrill\Request\Traits\AmountFormatterTrait;

/**
 * Class RefundRequest.
 */
final class RefundRequest
{
    use GetPayloadTrait;
    use AmountFormatterTrait;

    /**
     * $amount use only used for partial refunds.
     *
     * @param TransactionID $transactionId
     * @param Money|null    $amount
     */
    public function __construct(TransactionID $transactionId, Money $amount = null)
    {
        $this->payload = [
            'transaction_id' => (string)$transactionId,
        ];

        if (null != $amount) {
            $this->payload['amount'] = $this->formatToFloat($amount);
        }
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function setStatusUrl(Url $url): self
    {
        $this->payload['refund_status_url'] = (string)$url;

        return $this;
    }
}
