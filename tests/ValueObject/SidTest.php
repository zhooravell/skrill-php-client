<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Exception;
use DateTimeImmutable;
use Skrill\ValueObject\Sid;
use PHPUnit\Framework\TestCase;
use Skrill\Exception\InvalidSidException;

/**
 * Class SidTest.
 */
class SidTest extends TestCase
{
    /**
     * @throws InvalidSidException
     * @throws Exception
     */
    public function testSuccess()
    {
        $value = 'test123';
        $secretWord = new Sid($value, new DateTimeImmutable());

        self::assertEquals($value, (string) $secretWord);
    }

    /**
     * @throws InvalidSidException
     * @throws Exception
     */
    public function testEmptyValue()
    {
        self::expectException(InvalidSidException::class);
        self::expectExceptionMessage('Skrill sid should not be blank.');

        new Sid(' ', new DateTimeImmutable());
    }
}
