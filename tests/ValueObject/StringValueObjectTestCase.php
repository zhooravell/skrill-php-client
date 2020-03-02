<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;

/**
 * Class StringValueObjectTestCase
 */
class StringValueObjectTestCase extends TestCase
{
    /**
     * @return Generator
     */
    public function emptyStringDataProvider(): Generator
    {
        yield [''];
        yield [""];
        yield ['  '];
        yield ["\n"];
        yield ["\t"];
        yield ["\n\n"];
        yield ["\t\t"];
        yield ["\t\n"];
        yield ["\n\t"];
    }
}
