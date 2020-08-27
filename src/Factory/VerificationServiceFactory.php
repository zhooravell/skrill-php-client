<?php

declare(strict_types=1);

namespace Skrill\Factory;

use Psr\Http\Message\ResponseInterface;
use Skrill\Exception\SkrillResponseException;
use Skrill\Response\Response;

/**
 * Class VerificationServiceFactory
 */
final class VerificationServiceFactory
{
    /**
     * @param ResponseInterface $response
     * @return Response
     * @throws SkrillResponseException
     */
    public static function createFromCustomerVerificationResponse(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);

        if (JSON_ERROR_NONE == json_last_error()) {
            throw SkrillResponseException::fromSkillError($result->message);
        }

        return new Response($result);
    }
}
