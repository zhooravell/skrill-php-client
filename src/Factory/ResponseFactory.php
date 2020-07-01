<?php

declare(strict_types=1);

namespace Skrill\Factory;

use SimpleXMLElement;
use Skrill\Response\Response;
use Skrill\Exception\SkrillException;
use Psr\Http\Message\ResponseInterface;
use Skrill\Exception\SkrillResponseException;

/**
 * Class ResponseFactory.
 */
final class ResponseFactory
{
    /**
     * @param ResponseInterface $response
     *
     * @return Response
     *
     * @throws SkrillException
     */
    public static function createFromTransferResponse(ResponseInterface $response): Response
    {
        $xml = self::responseToXML($response);

        if (!$xml->xpath('transaction[(id) and (amount) and (currency) and (status)]')) {
            throw SkrillResponseException::invalidTransactionFormat();
        }

        return new Response((array) $xml->transaction);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     *
     * @throws SkrillResponseException
     */
    public static function createFromRefundResponse(ResponseInterface $response): Response
    {
        $xml = self::responseToXML($response);

        if (!$xml->xpath('transaction_id') ||
            !$xml->xpath('mb_amount') ||
            !$xml->xpath('mb_currency') ||
            !$xml->xpath('status')
        ) {
            throw SkrillResponseException::invalidTransactionFormat();
        }

        return new Response((array) $xml);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return SimpleXMLElement
     *
     * @throws SkrillResponseException
     */
    private static function responseToXML(ResponseInterface $response): SimpleXMLElement
    {
        $xml = new SimpleXMLElement($response->getBody()->getContents());

        if ($xml->xpath('error/error_msg')) {
            throw SkrillResponseException::fromSkillError($xml->error->error_msg);
        }

        return $xml;
    }
}
