<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\RecurringBillingNote;
use Skrill\Exception\InvalidRecurringBillingNoteException;

/**
 * Class RecurringBillingNoteTest.
 */
class RecurringBillingNoteTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidRecurringBillingNoteException
     */
    public function testSuccess()
    {
        $value = 'test123';

        self::assertSame($value, (string)(new RecurringBillingNote($value)));
    }

    /**
     * @throws InvalidRecurringBillingNoteException
     */
    public function testSuccess2()
    {
        self::assertSame('test123', (string)(new RecurringBillingNote(' test123 ')));
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidRecurringBillingNoteException
     */
    public function testEmptyValue(string $value)
    {
        $this->expectException(InvalidRecurringBillingNoteException::class);
        $this->expectExceptionMessage('Skrill recurring billing note should not be blank.');

        new RecurringBillingNote($value);
    }
}
