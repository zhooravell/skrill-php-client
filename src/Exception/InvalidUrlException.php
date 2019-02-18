<?php

declare(strict_types=1);

namespace Skrill\Exception;

/**
 * Class InvalidUrlException.
 */
final class InvalidUrlException extends \Exception implements SkrillException
{
    /**
     * @param string $url
     *
     * @return InvalidUrlException
     */
    public static function invalidUrl($url)
    {
        return new self(sprintf('"%s" is not a valid url.', $url));
    }
}
