<?php

namespace Centrobill\Skrill\Tests\Response;

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
        self::assertEquals($type, $item->getType());
        self::assertEquals($details, $item->getDetails());
        self::assertEquals($lesion, $item->getLesion());
        self::assertEquals($profit, $item->getProfit());
        self::assertEquals($status, $item->getStatus());
        self::assertEquals($balance, $item->getBalance());
        self::assertEquals($reference, $item->getReference());
        self::assertEquals($amount, $item->getAmount());
        self::assertEquals($currency, $item->getCurrency());
        self::assertEquals($info, $item->getInfo());
        self::assertEquals($skrillId, $item->getSkrillId());
        self::assertEquals($paymentType, $item->getPaymentType());
    }
}
