<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\RecurringBillingNote;
use Skrill\Exception\InvalidRecurringBillingNoteException;

/**
 * Class RecurringBillingNoteTest.
 */
class RecurringBillingNoteTest extends TestCase
{
    /**
     * @throws InvalidRecurringBillingNoteException
     */
    public function testSuccess()
    {
        $value = 'test123';
        $secretWord = new RecurringBillingNote($value);

        self::assertEquals($value, (string) $secretWord);
    }

    /**
     * @throws InvalidRecurringBillingNoteException
     */
    public function testEmptyValue()
    {
        self::expectException(InvalidRecurringBillingNoteException::class);
        self::expectExceptionMessage('Skrill recurring billing note should not be blank.');

        new RecurringBillingNote(' ');
    }
}
