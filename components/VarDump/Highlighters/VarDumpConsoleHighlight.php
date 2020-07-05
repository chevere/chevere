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
        $this->color = new Color;
        $color = $this->pallet()[$key] ?? 'reset';
        $this->style = is_string($color) ? [$color] : $color;
    }

    public function highlight(string $dump): string
    {
        foreach ($this->style as $style) {
            $dump = $this->color->apply("color[$style]", $dump);
        }

        return $dump;
    }

    public function pallet(): array
    {
        return [
            TypeInterface::STRING => '208', // orange
            TypeInterface::FLOAT => '208',
            TypeInterface::INTEGER => '208',
            TypeInterface::BOOLEAN => '208',
            TypeInterface::NULL => '208',
            TypeInterface::OBJECT => '220', // yellow
            TypeInterface::ARRAY => '41', // green
            TypeInterface::RESOURCE => '196', // red
            VarDumperInterface::FILE => '4', // blue
            VarDumperInterface::CLASS_REG => '221', // light yellow
            VarDumperInterface::OPERATOR => '242', // dark gray
            VarDumperInterface::FUNCTION => '39',
            VarDumperInterface::MODIFIERS => '207', // purple
            VarDumperInterface::VARIABLE => '208',
            VarDumperInterface::EMPHASIS => ['242', '3'] // dark gray italic
        ];
    }
}
