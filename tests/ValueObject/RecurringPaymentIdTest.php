<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
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

        self::assertEquals($value, new RecurringPaymentID($value));
    }

    /**
     * @throws InvalidRecurringPaymentIDException
     */
    public function testSuccess2()
    {
        self::assertEquals('test123', new RecurringPaymentID(' test123 '));
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
        self::expectException(InvalidRecurringPaymentIDException::class);
        self::expectExceptionMessage('Skrill recurring payment ID should not be blank.');

        new RecurringPaymentID($value);
    }
}
