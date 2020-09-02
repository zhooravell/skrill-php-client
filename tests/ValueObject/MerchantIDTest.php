<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\MerchantID;

/**
 * Class MerchantIDTest.
 */
class MerchantIDTest extends TestCase
{
    public function testSuccess()
    {
        self::assertSame(111, (new MerchantID(111))->getValue());
        self::assertSame(222, (new MerchantID(222))->getValue());
    }
}
