<?php

declare(strict_types=1);

namespace Skrill\Factory;

use DateTimeImmutable;
use Skrill\Response\HistoryItem;
use Skrill\Exception\SkrillResponseException;

/**
 * Class HistoryItemFactory
 */
class HistoryItemFactory
{
    /**
     * @param array $row
     *
     * @return HistoryItem
     * @throws SkrillResponseException
     */
    public static function createFromRow(array $row): HistoryItem
    {
        [
            ,
            $time,
            $type,
            $details,
            $lesion,
            $profit,
            $status,
            $balance,
            $reference,
            $amount,
            $currency,
            $info,
            $skrillId,
            $paymentType,
        ] = $row;

        if (!$datetime = DateTimeImmutable::createFromFormat('d M y H:i', $time)) {
            throw SkrillResponseException::fromSkillError(sprintf('Invalid time "%s".', $time));
        }

        return new HistoryItem(
            $reference,
            $skrillId,
            $datetime,
            $type,
            $details,
            $lesion,
            $profit,
            $status,
            $balance,
            $amount,
            $currency,
            $info,
            $paymentType
        );
    }
}
