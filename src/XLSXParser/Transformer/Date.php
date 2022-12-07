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
    public function transform(float|int $value): DateTimeImmutable
    {
        $format = 'd-m-Y H:i:s';

        $value = (int) floor(num: $value);
        /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        $unix = ($value - 25569) * 86400;
        $date = gmdate(format: $format, timestamp: $unix);

        return date_create_immutable_from_format(format: '!' . $format, datetime: $date);
    }
}
