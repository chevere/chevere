<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Wrappers;

use InvalidArgumentException;
use Chevere\Components\VarDump\Contracts\PalleteContract;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleWrapper extends AbstractWrapper
{
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

    public function wrap(string $dump): string
    {
        return $this->consoleColor
            ->apply($this->color, $dump);
    }

    public function pallete(): array
    {
        return PalleteContract::CONSOLE;
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
