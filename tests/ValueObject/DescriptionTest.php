<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Description;
use Skrill\Exception\InvalidDescriptionException;

/**
 * Class ProductDescriptionTest.
 */
class DescriptionTest extends TestCase
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
     * @throws InvalidDescriptionException
     */
    public function testEmptyText()
    {
        self::expectException(InvalidDescriptionException::class);
        self::expectExceptionMessage('Description text should not be blank.');

        new Description('Product ID:', '');
    }

    /**
     * @throws InvalidDescriptionException
     */
    public function testEmptyDescription()
    {
        self::expectException(InvalidDescriptionException::class);
        self::expectExceptionMessage('Description subject should not be blank.');

        new Description('', '4509334');
    }
}
