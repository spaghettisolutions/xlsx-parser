<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX\Transformer;

use DateTimeImmutable;

use function floor;
use function round;
use function sprintf;

final class Date
{
    protected DateTimeImmutable $baseDate;

    public function __construct()
    {
        $this->baseDate = new DateTimeImmutable(datetime: '1900-01-00 00:00:00 UTC');
    }

    public function transform(float|int $value): DateTimeImmutable
    {
        $days = floor(num: $value);

        $seconds = round(num: ($value - $days) * 86400);

        $date = clone $this->baseDate;
        $date->modify(modifier: sprintf('+%sday +%ssecond', $days - 1, $seconds));

        return $date;
    }
}
