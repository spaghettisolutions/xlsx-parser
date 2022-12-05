<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Transformer;

use DateTimeImmutable;

use function floor;
use function round;
use function sprintf;

/**
 * @internal
 */
final class Date
{
    private const BASEDATE = '1900-01-00 00:00:00 UTC';
    private DateTimeImmutable $baseDate;

    public function __construct()
    {
        $this->baseDate = new DateTimeImmutable(datetime: self::BASEDATE);
    }

    public function transform(float|int $value): DateTimeImmutable
    {
        $seconds = round(num: ($value - $days = floor(num: $value)) * 86400);

        $date = clone $this->baseDate;
        $date->modify(modifier: sprintf('+%sday +%ssecond', $days - 1, $seconds));

        return $date;
    }
}
