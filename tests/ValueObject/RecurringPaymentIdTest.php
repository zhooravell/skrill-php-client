<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\RecurringPaymentID;
use Skrill\Exception\InvalidRecurringPaymentIDException;

/**
 * Class RecurringPaymentIdTest.
 */
class RecurringPaymentIdTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidRecurringPaymentIDException
     */
    public function testSuccess()
    {
        $value = 'test123';

        self::assertSame($value, (string)(new RecurringPaymentID($value)));
    }

    /**
     * @throws InvalidRecurringPaymentIDException
     */
    public function testSuccess2()
    {
        self::assertSame('test123', (string)(new RecurringPaymentID(' test123 ')));
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidRecurringPaymentIDException
     */
    public function testEmptyValue(string $value)
    {
        $this->expectException(InvalidRecurringPaymentIDException::class);
        $this->expectExceptionMessage('Skrill recurring payment ID should not be blank.');

        new RecurringPaymentID($value);
    }
}
