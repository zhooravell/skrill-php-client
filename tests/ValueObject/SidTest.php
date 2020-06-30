<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Exception;
use DateTimeImmutable;
use Skrill\ValueObject\Sid;
use Skrill\Exception\InvalidSidException;

/**
 * Class SidTest.
 */
class SidTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidSidException
     * @throws Exception
     */
    public function testSuccess()
    {
        $value = 'test123';

        self::assertEquals($value, new Sid($value, new DateTimeImmutable()));
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidSidException
     * @throws Exception
     */
    public function testEmptyValue(string $value)
    {
        $this->expectException(InvalidSidException::class);
        $this->expectExceptionMessage('Skrill sid should not be blank.');

        new Sid($value, new DateTimeImmutable());
    }
}
