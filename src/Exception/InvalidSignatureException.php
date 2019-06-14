<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class InvalidSignatureException.
 */
final class InvalidSignatureException extends Exception implements SkrillException
{
    /**
     * @return InvalidSignatureException
     */
    public static function emptySignature(): self
    {
        return new self('Signature should not be blank.');
    }

    /**
     * @return InvalidSignatureException
     */
    public static function lowercase(): self
    {
        return new self('Signature should in uppercase.');
    }
}
