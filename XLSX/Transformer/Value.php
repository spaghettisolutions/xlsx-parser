<?php declare(strict_types = 1);

namespace SimpleToImplement\XLSXParser\XLSX\Transformer;

use SimpleToImplement\XLSXParser\XLSX\SharedStrings;
use SimpleToImplement\XLSXParser\XLSX\Styles;

final class Value
{
    public const TYPE_BOOL = 'b';
    public const TYPE_NUMBER = 'n';
    public const TYPE_ERROR = 'e';
    public const TYPE_SHARED_STRING = 's';
    public const TYPE_STRING = 'str';
    public const TYPE_INLINE_STRING = 'inlineStr';

    public function __construct(private readonly Date $dateTransformer, private readonly SharedStrings $sharedStrings, private readonly Styles $styles)
    {
    }

    public function transform(string $value, string $type, string $style): mixed
    {
        return match ($type) {
            self::TYPE_BOOL => '1' === $value,
            self::TYPE_SHARED_STRING => trim(string: $this->sharedStrings->get(index: (int) $value)),
            '', self::TYPE_NUMBER => $style && Styles::FORMAT_DATE === $this->styles->get(index: (int) $style)
                ? $this->dateTransformer->transform(value: (int) $value)
                : $value * 1,
            default => trim(string: $value),
        };
    }
}
