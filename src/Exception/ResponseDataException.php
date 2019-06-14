<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class ResponseDataException.
 */
final class ResponseDataException extends Exception implements SkrillException
{
    /**
     * @return ResponseDataException
     */
    public static function reedOnlyMode(): self
    {
        return new self('Can\'t modify response data');
    }
}
