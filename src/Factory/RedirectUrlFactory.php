<?php

declare(strict_types=1);

namespace Skrill\Factory;

use Skrill\ValueObject\Sid;

/**
 * Class RedirectUrlFactory.
 */
final class RedirectUrlFactory
{
    /**
     * @param Sid $sid
     *
     * @return string
     */
    public static function fromSid(Sid $sid)
    {
        return sprintf('https://pay.skrill.com/?sid=%s', $sid);
    }
}
