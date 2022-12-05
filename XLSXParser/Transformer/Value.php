<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Transformer;

use DateTimeImmutable;
use Spaghetti\XLSXParser\SharedStrings;
use Spaghetti\XLSXParser\Styles;

use function filter_var;
use function trim;

use const FILTER_VALIDATE_BOOL;

/**
 * @internal
 */
final class Value
{
    private const TYPE_BOOL = 'b';
    private const TYPE_EMPTY = '';
    private const TYPE_NUMBER = 'n';
    private const TYPE_SHARED_STRING = 's';

    private readonly Date $dateTransformer;

    public function __construct(private readonly SharedStrings $sharedStrings, private readonly Styles $styles, ?Date $dateTransformer = null)
    {
        $this->dateTransformer = $dateTransformer ?? new Date();
    }

    public function transform(string $value, string $type, string $style): string|int|bool|DateTimeImmutable
    {
        return match ($type) {
            self::TYPE_BOOL => filter_var(value: $value, filter: FILTER_VALIDATE_BOOL),
            self::TYPE_SHARED_STRING => trim(string: $this->sharedStrings->get(index: (int) $value)),
            self::TYPE_EMPTY, self::TYPE_NUMBER => $this->transformNumber(style: $style, value: (int) $value),
            default => trim(string: $value),
        };
    }

    private function transformNumber(string $style, int $value): DateTimeImmutable|int
    {
        return match (true) {
            $style && Styles::FORMAT_DATE === $this->styles->get(index: (int) $style) => $this->dateTransformer->transform(value: $value),
            default => $value,
        };
    }
}
