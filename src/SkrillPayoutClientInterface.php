<?php

declare(strict_types=1);

namespace Skrill;

use Skrill\ValueObject\Sid;
use Skrill\Response\Response;
use Skrill\Request\PayoutRequest;
use Skrill\Exception\SkrillException;

/**
 * You can use the Automated Payments Interface to make automated partial or full refunds to customers,
 * up to the amount of the original payment.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Automated_Payments_Interface_Guide.pdf
 */
interface SkrillPayoutClientInterface
{
    /**
     * Preparing a quick payout.
     *
     * @param PayoutRequest $request
     *
     * @return Sid
     *
     * @throws SkrillException
     */
    public function preparePayout(PayoutRequest $request): Sid;

    /**
     * Executing a payout.
     *
     * @param Sid $sid
     *
     * @return Response
     *
     * @throws SkrillException
     */
    public function executePayout(Sid $sid): Response;
}
