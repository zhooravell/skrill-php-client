<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidSignatureException.
 */
final class InvalidSignatureException extends \Exception implements SkrillException
{
    /**
     * @return InvalidSignatureException
     */
    public static function emptySignature()
    {
        return new self('Signature should not be blank.');
    }

    /**
     * @return InvalidSignatureException
     */
    public static function lowercase()
    {
        return new self('Signature should in uppercase.');
    }
}
