<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\Description;
use Skrill\Exception\InvalidDescriptionException;

/**
 * Class ProductDescriptionTest.
 */
class DescriptionTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidDescriptionException
     */
    public function testSuccess()
    {
        $desc = 'Product ID:';
        $text = '4509334';
        $productDescription = new Description($desc, $text);

        self::assertEquals($desc, $productDescription->getSubject());
        self::assertEquals($text, $productDescription->getText());
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidDescriptionException
     */
    public function testEmptyText(string $value)
    {
        self::expectException(InvalidDescriptionException::class);
        self::expectExceptionMessage('Description text should not be blank.');

        new Description('Product ID:', $value);
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidDescriptionException
     */
    public function testEmptyDescription(string $value)
    {
        self::expectException(InvalidDescriptionException::class);
        self::expectExceptionMessage('Description subject should not be blank.');

        new Description($value, '4509334');
    }
}
