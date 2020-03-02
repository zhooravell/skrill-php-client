<?php

declare(strict_types=1);

namespace Skrill\Exception;

use Exception;
use Skrill\ValueObject\CompanyName;

/**
 * Class InvalidCompanyNameException.
 */
final class InvalidCompanyNameException extends Exception implements SkrillException
{
    /**
     * @return InvalidCompanyNameException
     */
    public static function invalidMaxLength(): self
    {
        return new self(
            sprintf('The length of company name should not exceed %d characters.', CompanyName::MAX_LENGTH)
        );
    }
}
