<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class SkrillResponseException.
 */
final class SkrillResponseException extends \Exception implements SkrillException
{
    /**
     * @param string $error
     *
     * @return SkrillResponseException
     */
    public static function fromSkillError($error)
    {
        return new self(sprintf('Skrill error: %s', $error));
    }

    /**
     * @return SkrillResponseException
     */
    public static function invalidTransactionFormat()
    {
        return new self('Skrill invalid response format with transaction.');
    }
}
