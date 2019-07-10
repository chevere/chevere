<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere\VarDumper;

use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Wraps dump data into colored output for HTML and console.
 */
class Wrapper
{
    /** @var string */
    public $key;

    /** @var string */
    public $dump;

    /** @var ConsoleColor */
    public $consoleColor;

    /** @var bool */
    protected $isCLI = false;

    /**
     * @param string $key color palette key
     */
    public function __construct(string $key, string $dump)
    {
        $this->key = $key;
        $this->dump = $dump;
    }

    public function setCLI(ConsoleColor $consoleColor)
    {
        $this->consoleColor = $consoleColor;
        $this->isCLI = true;
    }

    /**
     * TODO: toString interface.
     *
     * @return string color
     */
    public function toString(): string
    {
        return $this->wrap($this->dump) ?? '';
    }

    /**
     * Get color for palette key.
     *
     * @return string color
     */
    public function getColor(): string
    {
        if ($this->isCLI) {
            return Pallete::CONSOLE[$this->key] ?? '';
        }

        return Pallete::PALETTE[$this->key] ?? '';
    }

    // static @ wrap(string $key, $dump)

    /**
     * Wrap dump data.
     *
     * @param string $dump dump data
     *
     * @return string wrapped dump data
     */
    public function wrap(string $dump): ?string
    {
        if ($color = $this->getColor()) {
            if ($this->isCLI) {
                return $this->consoleColor->apply($color, $dump);
            }

            return '<span style="color:'.$color.'">'.$dump.'</span>';
        } else {
            return $dump;
        }
    }
}
