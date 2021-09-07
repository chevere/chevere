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
use Throwable;

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
            try {
                $dump = $this->color->apply("color[${style}]", $dump);
            } catch (Throwable $e) {
            }
        }

        return $dump;
    }

    public function palette(): array
    {
        return [
            // DarkOrange
            TypeInterface::STRING => '208',
            TypeInterface::FLOAT => '208',
            TypeInterface::INTEGER => '208',
            TypeInterface::BOOLEAN => '208',
            TypeInterface::NULL => '208',
            // Gold1
            TypeInterface::OBJECT => '220',
            // Green3
            TypeInterface::ARRAY => '41',
            // IndianRed1
            TypeInterface::RESOURCE => '203',
            // SkyBlue2
            VarDumperInterface::FILE => '111',
            // light yellow
            VarDumperInterface::CLASS_REG => '221',
            // Grey42
            VarDumperInterface::OPERATOR => '242',
            // DeepSkyBlue1
            VarDumperInterface::FUNCTION => '39',
            VarDumperInterface::VARIABLE => '39',
            // Orchid
            VarDumperInterface::MODIFIERS => '170',
            // dark gray italic
            VarDumperInterface::EMPHASIS => ['242', '3'],
        ];
    }
}
