<?php

declare(strict_types=1);

namespace Skrill\Tests\Request;

use Money\Currencies\ISOCurrencies;
use Money\Money;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\Url;
use PHPUnit\Framework\TestCase;
use Skrill\Request\OnDemandRequest;
use Skrill\ValueObject\TransactionID;
use Skrill\ValueObject\RecurringPaymentID;

/**
 * Class OnDemandRequestTest.
 */
class OnDemandRequestTest extends TestCase
{
    /**
     * @throws \Skrill\Exception\InvalidRecurringPaymentIDException
     * @throws \Skrill\Exception\InvalidTransactionIDException
     * @throws \Skrill\Exception\InvalidUrlException
     */
    public function testSuccess()
    {
        $parser = new DecimalMoneyParser(new ISOCurrencies());

        $request = new OnDemandRequest(
            new RecurringPaymentID('222'),
            new TransactionID(111),
            $parser->parse('1000000.51', 'EUR')
        );

        self::assertEquals(
            [
                'frn_trn_id' => '111',
                'rec_payment_id' => '222',
                'currency' => 'EUR',
                'amount' => 1000000.51,
            ],
            $request->getPayload()
        );

        $res = $request
            ->setStatusUrl(new Url('https://google.com/1'))
        ;

        self::assertInstanceOf(OnDemandRequest::class, $res);

        self::assertEquals(
            [
                'frn_trn_id' => '111',
                'rec_payment_id' => '222',
                'currency' => 'EUR',
                'amount' => 1000000.51,
                'ondemand_status_url' => 'https://google.com/1',
            ],
            $request->getPayload()
        );
    }
}
