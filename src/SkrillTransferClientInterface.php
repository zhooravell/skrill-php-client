<?php

declare(strict_types=1);

namespace Skrill;

use Skrill\ValueObject\Sid;
use Skrill\Response\Response;
use Skrill\Request\TransferRequest;
use Skrill\Exception\SkrillException;

/**
 * SEND MONEY.
 *
 * You can make mass payments using the Skrill Automated Payments Interface (API).
 * This offers the same functionality that is available on My Account,
 * but it allows you to automate the sending of payment details from your
 * servers to Skrill using an HTTPs request.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Automated_Payments_Interface_Guide.pdf
 */
interface SkrillTransferClientInterface
{
    /**
     * Sending a transfer prepare request.
     *
     * @param TransferRequest $request
     *
     * @return Sid
     *
     * @throws SkrillException
     */
    public function prepareTransfer(TransferRequest $request): Sid;

    /**
     * Executing a transfer request.
     *
     * @param Sid $sid
     *
     * @return Response
     *
     * @throws SkrillException
     */
    public function executeTransfer(Sid $sid): Response;
}
