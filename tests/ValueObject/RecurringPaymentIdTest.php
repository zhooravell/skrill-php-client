<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\RecurringPaymentID;
use Skrill\Exception\InvalidRecurringPaymentIdException;

/**
 * Class RecurringPaymentIdTest.
 */
class RecurringPaymentIdTest extends TestCase
{
    /**
     * @throws InvalidRecurringPaymentIdException
     */
    public function testSuccess()
    {
        $value = 'test123';
        $authKey = new RecurringPaymentID($value);

        self::assertEquals($value, (string) $authKey);
    }

    /**
     * @throws InvalidRecurringPaymentIdException
     */
    public function testEmptyValue()
    {
        self::expectException(InvalidRecurringPaymentIdException::class);
        self::expectExceptionMessage('Skrill recurring payment ID should not be blank.');

        new RecurringPaymentID(' ');
    }
}
