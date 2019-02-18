<?php

namespace Skrill\Tests\Factory;

use Skrill\Factory\SidFactory;
use PHPUnit\Framework\TestCase;
use Skrill\Exception\InvalidSidException;

/**
 * Class SidFactoryTest.
 */
class SidFactoryTest extends TestCase
{
    /**
     * @throws InvalidSidException
     * @throws \Exception
     */
    public function testSuccess()
    {
        $rawSid = 'test';
        $sid = SidFactory::createFromString($rawSid);

        self::assertEquals($rawSid, (string) $sid);

        $now = new \DateTime();
        $diff = $now->diff($sid->getExpirationTillDateTime());

        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            self::assertEquals(14, $diff->i);
            self::assertEquals(59, $diff->s);
        } else {
            self::assertEquals(15, $diff->i);
        }
    }
}
