<?php

declare(strict_types=1);

namespace Skrill;

use Skrill\ValueObject\Sid;
use Skrill\Response\Response;
use Skrill\Request\OnDemandRequest;
use Skrill\Exception\SkrillException;

/**
 * SKRILL 1-TAP PAYMENT.
 *
 * Skrill offers a single-click payment service which enables you
 * to automatically debit transactions from your customer’s
 * Skrill account without the customer having to login to
 * their account and authorise each time.
 */
interface SkrillOnDemandClientInterface
{
    /**
     * @param OnDemandRequest $request
     *
     * @return Sid
     *
     * @throws SkrillException
     */
    public function prepareOnDemand(OnDemandRequest $request): Sid;

    /**
     * @param Sid $sid
     *
     * @return Response
     *
     * @throws SkrillException
     */
    public function executeOnDemand(Sid $sid): Response;
}
