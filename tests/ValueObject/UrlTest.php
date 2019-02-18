<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Url;
use Skrill\Exception\InvalidUrlException;

/**
 * Class UrlTest.
 */
class UrlTest extends TestCase
{
    /**
     * @throws InvalidUrlException
     */
    public function testEmpty()
    {
        self::expectException(InvalidUrlException::class);
        self::expectExceptionMessage('"" is not a valid url.');

        new Url('');
    }

    /**
     * @throws InvalidUrlException
     */
    public function testSuccess()
    {
        $url = 'https://api.com';
        $baseApiUrl = new Url($url);

        self::assertEquals($url, (string) $baseApiUrl);
    }

    /**
     * @throws InvalidUrlException
     */
    public function testInvalidUrl()
    {
        self::expectException(InvalidUrlException::class);
        self::expectExceptionMessage('"localhost" is not a valid url.');

        new Url('localhost');
    }
}
