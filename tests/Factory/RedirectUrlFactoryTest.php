<?php

namespace Skrill\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Skrill\Factory\SidFactory;
use Skrill\Factory\RedirectUrlFactory;
use Skrill\Exception\InvalidSidException;

/**
 * Class RedirectUrlFactoryTest.
 */
class RedirectUrlFactoryTest extends TestCase
{
    /**
     * @throws InvalidSidException
     */
    public function testSuccess()
    {
        self::assertSame(
            'https://pay.skrill.com/?sid=6d7d0005655018a3ef7abd043ee31cfd',
            RedirectUrlFactory::fromSid(SidFactory::createFromString('6d7d0005655018a3ef7abd043ee31cfd'))
        );
    }
}
