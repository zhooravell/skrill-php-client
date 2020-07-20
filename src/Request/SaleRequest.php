<?php

declare(strict_types=1);

namespace Skrill\Request;

use Money\Money;
use Skrill\ValueObject\Url;
use Skrill\ValueObject\Email;
use Skrill\ValueObject\Language;
use Skrill\ValueObject\Description;
use Skrill\ValueObject\TransactionID;
use Skrill\Request\Traits\GetPayloadTrait;
use Skrill\ValueObject\RecurringBillingNote;
use Skrill\Request\Traits\AmountFormatterTrait;

/**
 * Class SaleRequest.
 */
final class SaleRequest {
    use GetPayloadTrait;
    use AmountFormatterTrait;

    /**
     * @param TransactionID $transactionId
     * @param Money $amount
     */
    public function __construct(TransactionID $transactionId, Money $amount) {
        $this->payload = [
            'transaction_id' => strval($transactionId),
            'currency' => strval($amount->getCurrency()),
            'amount' => $this->formatToFloat($amount),
        ];
    }

    /**
     * @param Language $lang
     *
     * @return SaleRequest
     */
    public function setLang(Language $lang): self {
        $this->payload['language'] = strval($lang);

        return $this;
    }

    /**
     * @param Email $email
     *
     * @return SaleRequest
     */
    public function setPayFromEmail(Email $email): self {
        $this->payload['pay_from_email'] = strval($email);

        return $this;
    }

    /**
     *
     * @param string $first_name
     * @return SaleRequest
     */
    public function setFirstName(string $first_name): self {
        $this->payload['first_name'] = $first_name;

        return $this;
    }

    /**
     *
     * @param string $last_name
     * @return SaleRequest
     */
    public function setLastName(string $last_name): self {
        $this->payload['last_name'] = $last_name;

        return $this;
    }

    /**
     * The detail1_description combined with the detail1_text is shown in the more information field of the merchant
     * account history CSV file.
     *
     * Example:
     * - detail1_description: "Product ID:"
     * - detail1_text: "4509334"
     *
     * Using the example values, this would be "Product ID: 4509334".
     *
     * @param Description $productDescription
     *
     * @return SaleRequest
     */
    public function setProductDescription(Description $productDescription): self {
        $this->payload['detail1_description'] = $productDescription->getSubject();
        $this->payload['detail1_text'] = $productDescription->getText();

        return $this;
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function setReturnUrl(Url $url): self {
        $this->payload['return_url'] = strval($url);

        return $this;
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function setCancelUrl(Url $url): self {
        $this->payload['cancel_url'] = strval($url);

        return $this;
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function setStatusUrl(Url $url): self {
        $this->payload['status_url'] = strval($url);

        return $this;
    }

    /**
     * @param RecurringBillingNote $note
     * @param Money $money
     *
     * @return $this
     */
    public function enableRecurringBilling(RecurringBillingNote $note, Money $money): self {
        $this->payload['ondemand_max_amount'] = $this->formatToFloat($money);
        $this->payload['ondemand_max_currency'] = strval($money->getCurrency());
        $this->payload['ondemand_note'] = strval($note);

        return $this;
    }
}
