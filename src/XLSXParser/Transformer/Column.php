<?php declare(strict_types = 1);

namespace Spaghetti\XLSXParser\Transformer;

use function ord;
use function str_split;

/**
 * @internal
 */
final class Column
{
    public function transform(string $name): int
    {
        $number = -1;

        foreach (str_split(string: $name) as $char) {
            $digit = ord(character: $char) - 65;
            if ($digit < 0) {
                break;
            }

            $number = ($number + 1) * 26 + $digit;
        }

        return $number;
    }
}
