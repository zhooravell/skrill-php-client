<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidDescriptionException;

/**
 * Value object for additional details.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class Description
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $text;

    /**
     * @param string $subject
     * @param string $text
     *
     * @throws InvalidDescriptionException
     */
    public function __construct(string $subject, string $text)
    {
        $subject = trim($subject);

        if (empty($subject)) {
            throw InvalidDescriptionException::emptySubject();
        }

        $text = trim($text);

        if (empty($text)) {
            throw InvalidDescriptionException::emptyText();
        }

        $this->subject = $subject;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
