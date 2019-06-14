<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidRecurringBillingNoteException.
 */
final class InvalidRecurringBillingNoteException extends Exception implements SkrillException
{
    /**
     * @return InvalidRecurringBillingNoteException
     */
    public static function emptyNote(): self
    {
        return new self('Skrill recurring billing note should not be blank.');
    }
}
