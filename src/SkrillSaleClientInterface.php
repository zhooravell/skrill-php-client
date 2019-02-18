<?php

declare(strict_types=1);

namespace Skrill;

use Skrill\ValueObject\Sid;
use Skrill\Request\SaleRequest;
use Skrill\Exception\SkrillException;

/**
 * Interface SkrillSaleClientInterface.
 */
interface SkrillSaleClientInterface
{
    /**
     * @param SaleRequest $request
     *
     * @return Sid
     *
     * @throws SkrillException
     */
    public function prepareSale(SaleRequest $request): Sid;
}
