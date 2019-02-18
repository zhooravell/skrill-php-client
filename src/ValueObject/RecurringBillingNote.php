<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\ValueObject\Traits\ValueToStringTrait;
use Skrill\Exception\InvalidRecurringBillingNoteException;

/**
 * Class RecurringBillingNote.
 */
final class RecurringBillingNote
{
    use ValueToStringTrait;

    /**
     * @param string $value
     *
     * @throws InvalidRecurringBillingNoteException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw InvalidRecurringBillingNoteException::emptyNote();
        }

        $this->value = $value;
    }
}
