<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Highlighters;

use Chevere\Components\VarDump\Highlighters\Traits\AssertKeyTrait;
use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarDumpHighlightInterface;
use Colors\Color;

final class VarDumpConsoleHighlight implements VarDumpHighlightInterface
{
    use AssertKeyTrait;

    private Color $color;

    private array $style;

    public function __construct(string $key)
    {
        $this->assertKey($key);
        $this->color = new Color();
        $color = $this->palette()[$key] ?? 'reset';
        $this->style = is_string($color) ? [$color] : $color;
    }

    public function highlight(string $dump): string
    {
        foreach ($this->style as $style) {
            $dump = $this->color->apply("color[${style}]", $dump);
        }

        return $dump;
    }

    public function palette(): array
    {
        // string text ffd76d
        // number null reference c39ac9
        // boolean ff657a
        // object namespace 9cd1bb
        // file b2b9bd
        return [
            // orange
            TypeInterface::STRING => '208',
            TypeInterface::FLOAT => '208',
            TypeInterface::INTEGER => '208',
            TypeInterface::BOOLEAN => '208',
            TypeInterface::NULL => '208',
            // yellow
            TypeInterface::OBJECT => '220',
            // green
            TypeInterface::ARRAY => '41',
            // red
            TypeInterface::RESOURCE => '196',
            // blue
            VarDumperInterface::FILE => '4',
            // light yellow
            VarDumperInterface::CLASS_REG => '221',
            // dark gray
            VarDumperInterface::OPERATOR => '242',
            VarDumperInterface::FUNCTION => '39',
            // purple
            VarDumperInterface::MODIFIERS => '207',
            VarDumperInterface::VARIABLE => '39',
            // dark gray italic
            VarDumperInterface::EMPHASIS => ['242', '3'],
        ];
    }
}
