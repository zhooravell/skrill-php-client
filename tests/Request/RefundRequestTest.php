<?php

declare(strict_types=1);

namespace Skrill\Tests\Request;

use Skrill\ValueObject\Url;
use PHPUnit\Framework\TestCase;
use Skrill\Request\RefundRequest;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;

/**
 * Class RefundRequestTest.
 */
class RefundRequestTest extends TestCase
{
    /**
     * @throws \Skrill\Exception\InvalidTransactionIDException
     * @throws \Skrill\Exception\InvalidUrlException
     */
    public function testSuccess()
    {
        $request = new RefundRequest(new TransactionID(111));

        self::assertEquals(
            [
                'transaction_id' => '111',
            ],
            $request->getPayload()
        );

        self::assertInstanceOf(RefundRequest::class, $request->setStatusUrl(new Url('https://google.com/1')));

        self::assertEquals(
            [
                'transaction_id' => '111',
                'refund_status_url' => 'https://google.com/1',
            ],
            $request->getPayload()
        );
    }

    /**
     * @throws \Skrill\Exception\InvalidTransactionIDException
     * @throws \Skrill\Exception\InvalidUrlException
     */
    public function testPartialSuccess()
    {
        $parser = new DecimalMoneyParser(new ISOCurrencies());

        $request = new RefundRequest(new TransactionID(111), $parser->parse('2.5', 'USD'));

        self::assertEquals(
            [
                'transaction_id' => '111',
                'amount' => 2.5,
            ],
            $request->getPayload()
        );

        self::assertInstanceOf(RefundRequest::class, $request->setStatusUrl(new Url('https://google.com/1')));

        self::assertEquals(
            [
                'transaction_id' => '111',
                'amount' => 2.5,
                'refund_status_url' => 'https://google.com/1',
            ],
            $request->getPayload()
        );
    }
}
