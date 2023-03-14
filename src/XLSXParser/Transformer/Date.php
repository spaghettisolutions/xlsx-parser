<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Transformer;

use DateTimeImmutable;

use function date_create_immutable_from_format;
use function floor;
use function gmdate;

/**
 * @internal
 */
final class Date
{
    private const DATETIME_FORMAT = 'd-m-Y H:i:s';

    public function transform(float|int $value): DateTimeImmutable
    {
        $value = (int) floor(num: $value);
        /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        $unix = ($value - 25569) * 86400;
        $date = gmdate(format: self::DATETIME_FORMAT, timestamp: $unix);

        return date_create_immutable_from_format(format: '!' . self::DATETIME_FORMAT, datetime: $date);
    }
}
