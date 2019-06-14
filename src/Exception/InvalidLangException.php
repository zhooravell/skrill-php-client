<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidLangException.
 */
final class InvalidLangException extends Exception implements SkrillException
{
    /**
     * @return InvalidLangException
     */
    public static function invalidValue(): self
    {
        return new self('Not accepted language by Skrill.');
    }
}
