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

namespace Chevere\Components\VarDump\Wrappers;

use Chevere\Components\Type\Interfaces\TypeInterface;
use InvalidArgumentException;
use Chevere\Components\VarDump\Interfaces\VarInfoInterface;
use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use Chevere\Components\VarDump\Wrappers\Traits\AssertKeyTrait;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleHighlight implements HighlightInterface
{
    use AssertKeyTrait;

    private ConsoleColor $consoleColor;

    private $color;

    public function __construct(string $key)
    {
        $this->assertKey($key);
        $this->key = $key;
        $this->consoleColor = new ConsoleColor();
        $color = $this->pallete()[$this->key] ?? 'default';
        $this->assertColor($color);
        $this->color = $color;
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
            TypeInterface::BOOLEAN => 'color_163', // purple
            TypeInterface::NULL => 'color_245', // grey
            TypeInterface::OBJECT => 'color_39',
            TypeInterface::ARRAY => 'color_41', // green
            TypeInterface::RESOURCE => 'color_147', // blue
            VarInfoInterface::_FILE => 'default',
            VarInfoInterface::_CLASS => 'color_147', // blue
            VarInfoInterface::_OPERATOR => 'color_245', // grey
            VarInfoInterface::_FUNCTION => 'color_39',
            VarInfoInterface::_PRIVACY => 'color_133',
            VarInfoInterface::_VARIABLE => 'color_208',
            VarInfoInterface::_EMPHASIS => ['color_245', 'italic']
        ];
    }

    private function assertColor($color): void
    {
        if (is_string($color)) {
            $color = [$color];
        }
        if (!is_array($color)) {
            throw new InvalidArgumentException('Style must be string or array.');
        }
    }
}
