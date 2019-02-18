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

        self::assertEquals(14, $diff->i);
        self::assertEquals(59, $diff->s);
    }
}
