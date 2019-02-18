<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidProductDescriptionException.
 */
final class InvalidDescriptionException extends \Exception implements SkrillException
{
    /**
     * @return InvalidDescriptionException
     */
    public static function emptySubject()
    {
        return new self('Description subject should not be blank.');
    }

    /**
     * @return InvalidDescriptionException
     */
    public static function emptyText()
    {
        return new self('Description text should not be blank.');
    }
}
