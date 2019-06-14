<?php

declare(strict_types=1);

namespace Skrill\Tests\Request;

use Skrill\ValueObject\Url;
use PHPUnit\Framework\TestCase;
use Money\Currencies\ISOCurrencies;
use Skrill\Request\OnDemandRequest;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Skrill\Exception\InvalidUrlException;
use Skrill\ValueObject\RecurringPaymentID;
use Skrill\Exception\InvalidTransactionIDException;
use Skrill\Exception\InvalidRecurringPaymentIDException;

/**
 * Class OnDemandRequestTest.
 */
class OnDemandRequestTest extends TestCase
{
    /**
     * @throws InvalidRecurringPaymentIDException
     * @throws InvalidTransactionIDException
     * @throws InvalidUrlException
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

        $res = $request->setStatusUrl(new Url('https://google.com/1'));

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
