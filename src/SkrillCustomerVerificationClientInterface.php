<?php

declare(strict_types=1);

namespace Skrill;

use Skrill\Exception\SkrillException;
use Skrill\Request\CustomerVerificationRequest;
use Skrill\Response\Response;

/**
 * The customer verification service is used to check if one of your customers, identified by an email
 * address or customer ID, is registered with Skrill (i.e. the customer already has an active Skrill Digital
 * Wallet account
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Customer_Verification_Service_Guide_v1.1__1_.pdf
 */
interface SkrillCustomerVerificationClientInterface
{
    /**
     * @param CustomerVerificationRequest $request
     * @return Response
     * @throws SkrillException
     */
    public function executeCustomerVerification(CustomerVerificationRequest $request): Response;
}
