<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidRecurringBillingNoteException.
 */
final class InvalidRecurringBillingNoteException extends \Exception implements SkrillException
{
    /**
     * @return InvalidRecurringBillingNoteException
     */
    public static function emptyNote()
    {
        return new self('Skrill recurring billing note should not be blank.');
    }
}
