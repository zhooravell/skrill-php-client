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

        self::assertSame($value, (string)(new TransactionID($value)));
    }

    /**
     * @throws InvalidTransactionIDException
     */
    public function testSuccess2()
    {
        self::assertSame('test123', (string)(new TransactionID(' test123 ')));
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
        $this->expectException(InvalidTransactionIDException::class);
        $this->expectExceptionMessage('Skrill transaction ID should not be blank.');

        new TransactionID($value);
    }
}
