<?php

namespace Skrill\Tests\Response;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Skrill\Response\HistoryItem;

/**
 * Class HistoryItemTest.
 */
class HistoryItemTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test()
    {
        $time = new DateTimeImmutable();
        $type = 'Send Money';
        $details = 'to nkoptel@centrobill.com';
        $lesion = '.5';
        $profit = '';
        $status = 'processed';
        $balance = '558.513759';
        $reference = '';
        $amount = '0.5';
        $currency = 'USD';
        $info = 'Test trasfer subject';
        $skrillId = '2450853001';
        $paymentType = 'WLT';

        $item = new HistoryItem(
            $reference,
            $skrillId,
            $time,
            $type,
            $details,
            $lesion,
            $profit,
            $status,
            $balance,
            $amount,
            $currency,
            $info,
            $paymentType
        );

        self::assertEquals($time, $item->getTime());
        self::assertSame($type, $item->getType());
        self::assertSame($details, $item->getDetails());
        self::assertSame($lesion, $item->getLesion());
        self::assertSame($profit, $item->getProfit());
        self::assertSame($status, $item->getStatus());
        self::assertSame($balance, $item->getBalance());
        self::assertSame($reference, $item->getReference());
        self::assertSame($amount, $item->getAmount());
        self::assertSame($currency, $item->getCurrency());
        self::assertSame($info, $item->getInfo());
        self::assertSame($skrillId, $item->getSkrillId());
        self::assertSame($paymentType, $item->getPaymentType());
    }
}
