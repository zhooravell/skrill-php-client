<?php

declare(strict_types=1);

namespace Skrill;

use ArrayObject;
use DateTimeInterface;
use Skrill\Exception\SkrillException;

/**
 * The Merchant Query Interface allows you to query the Skrill database for the current status of
 * your transactions as well as perform actions connected to Skrill 1-Tap and recurring payments.
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Automated_Payments_Interface_Guide.pdf
 */
interface SkrillHistoryClientInterface
{
    /**
     * View account history.
     *
     * @param DateTimeInterface      $startDate
     * @param DateTimeInterface|null $endDate
     *
     * @return ArrayObject
     *
     * @throws SkrillException
     */
    public function viewHistory(DateTimeInterface $startDate, DateTimeInterface $endDate = null): ArrayObject;
}
