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

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use Chevere\Components\VarDump\Highlighters\Traits\AssertKeyTrait;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleHighlight implements HighlightInterface
{
    use AssertKeyTrait;

    private ConsoleColor $consoleColor;

    private array $color;

    public function __construct(string $key)
    {
        $this->assertKey($key);
        $this->consoleColor = new ConsoleColor();
        $color = $this->pallete()[$key] ?? 'default';
        $this->color = is_string($color) ? [$color] : $color;
    }

    public function wrap(string $dump): string
    {
        return $this->consoleColor
            ->apply($this->color, $dump);
    }

    public function pallete(): array
    {
        return [
            TypeInterface::STRING => 'color_11',
            TypeInterface::FLOAT => 'color_11',
            TypeInterface::INTEGER => 'color_11',
            TypeInterface::BOOLEAN => 'color_163',
            TypeInterface::NULL => 'color_245',
            TypeInterface::OBJECT => 'color_39',
            TypeInterface::ARRAY => 'color_41',
            TypeInterface::RESOURCE => 'color_147',
            VarDumperInterface::_FILE => 'default',
            VarDumperInterface::_CLASS => 'color_147',
            VarDumperInterface::_OPERATOR => 'color_245',
            VarDumperInterface::_FUNCTION => 'color_39',
            VarDumperInterface::_MODIFIERS => 'color_133',
            VarDumperInterface::_VARIABLE => 'color_208',
            VarDumperInterface::_EMPHASIS => ['color_245', 'italic']
        ];
    }
}
