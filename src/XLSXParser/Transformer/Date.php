<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Transformer;

use DateTimeImmutable;

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
        $base = 25569;

        $value = (int) floor(num: $value);
        /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
        $unix = ($value - $base) * 86400;
        $date = gmdate(format: $format, timestamp: $unix);

        return DateTimeImmutable::createFromFormat(format: '!' . $format, datetime: $date);
    }
}
