<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\TransactionID;
use Skrill\Exception\InvalidTransactionIDException;

/**
 * Class TransactionIDTest.
 */
class TransactionIDTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidTransactionIDException
     */
    public function testSuccess()
    {
        $value = 'test123';

        self::assertEquals($value, new TransactionID($value));
    }

    /**
     * @throws InvalidTransactionIDException
     */
    public function testSuccess2()
    {
        self::assertEquals('test123', new TransactionID(' test123 '));
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidTransactionIDException
     */
    public function testEmptyValue(string $value)
    {
        self::expectException(InvalidTransactionIDException::class);
        self::expectExceptionMessage('Skrill transaction ID should not be blank.');

        new TransactionID($value);
    }
}
