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
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use Chevere\Components\VarDump\Interfaces\WrapperInterface;
use Chevere\Components\VarDump\Wrappers\Traits\AssertKeyTrait;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleWrapper implements WrapperInterface
{
    use AssertKeyTrait;

    private ConsoleColor $consoleColor;

    private $color;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->assertKey();
        $this->consoleColor = new ConsoleColor();
        $this->color = $this->pallete()[$this->key];
        $this->assertColor();
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
            VarDumpInterface::_FILE => 'default',
            VarDumpInterface::_CLASS => 'color_147', // blue
            VarDumpInterface::_OPERATOR => 'color_245', // grey
            VarDumpInterface::_FUNCTION => 'color_39',
            VarDumpInterface::_PRIVACY => 'color_133',
            VarDumpInterface::_VARIABLE => 'color_208',
            VarDumpInterface::_EMPHASIS => ['color_245', 'italic']
        ];
    }

    public function wrap(string $dump): string
    {
        return $this->consoleColor
            ->apply($this->color, $dump);
    }

    private function assertColor(): void
    {
        if (is_string($this->color)) {
            $this->color = [$this->color];
        }
        if (!is_array($this->color)) {
            throw new InvalidArgumentException('Style must be string or array.');
        }
    }
}
