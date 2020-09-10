<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\Url;
use Skrill\Exception\InvalidUrlException;

/**
 * Class UrlTest.
 */
class UrlTest extends StringValueObjectTestCase
{
    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidUrlException
     */
    public function testEmpty(string $value)
    {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionMessage('"" is not a valid url.');

        new Url($value);
    }

    /**
     * @throws InvalidUrlException
     */
    public function testSuccess()
    {
        $url = 'https://api.com';

        self::assertSame($url, (string)(new Url($url)));
    }

    /**
     * @throws InvalidUrlException
     */
    public function testSuccess2()
    {
        self::assertSame('https://api.com', (string)(new Url(' https://api.com ')));
    }

    /**
     * @throws InvalidUrlException
     */
    public function testInvalidUrl()
    {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionMessage('"localhost" is not a valid url.');

        new Url('localhost');
    }
}
