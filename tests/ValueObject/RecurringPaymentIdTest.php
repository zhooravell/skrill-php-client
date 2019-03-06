<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\RecurringPaymentID;
use Skrill\Exception\InvalidRecurringPaymentIDException;

/**
 * Class RecurringPaymentIdTest.
 */
class RecurringPaymentIdTest extends TestCase
{
    /**
     * @throws InvalidRecurringPaymentIDException
     */
    public function testSuccess()
    {
        $value = 'test123';
        $authKey = new RecurringPaymentID($value);

        self::assertEquals($value, (string) $authKey);
    }

    /**
     * @throws InvalidRecurringPaymentIDException
     */
    public function testEmptyValue()
    {
        self::expectException(InvalidRecurringPaymentIDException::class);
        self::expectExceptionMessage('Skrill recurring payment ID should not be blank.');

        new RecurringPaymentID(' ');
    }
}
