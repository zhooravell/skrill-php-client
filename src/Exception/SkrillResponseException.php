<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;

/**
 * Class SkrillResponseException.
 */
final class SkrillResponseException extends Exception implements SkrillException
{
    /**
     * @param string $error
     *
     * @return SkrillResponseException
     */
    public static function fromSkillError($error): self
    {
        return new self(sprintf('Skrill error: %s', $error));
    }

    /**
     * @return SkrillResponseException
     */
    public static function invalidTransactionFormat(): self
    {
        return new self('Skrill invalid response format with transaction.');
    }
}
