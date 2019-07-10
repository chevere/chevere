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

    public function useCLI(bool $boolean)
    {
        $this->consoleColor = new ConsoleColor();
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
    }

    public function getCLIColor(): ?string
    {
        return Pallete::CONSOLE[$this->key] ?? null;
    }

    public function getHTMLColor(): ?string
    {
        return Pallete::PALETTE[$this->key] ?? null;
    }

    public function wrapCLI(): string
    {
        if ($color = $this->getCLIColor($this->key)) {
            return $this->consoleColor->apply($color, $this->dump);
        }

        return $this->dump;
    }

    public function wrapHTML()
    {
        if ($color = $this->getHTMLColor($this->key)) {
            return '<span style="color:'.$color.'">'.$this->dump.'</span>';
        }

        return $this->dump;
    }

    /**
     * Wrap dump data.
     *
     * @return string wrapped dump data
     */
    public function wrap(): string
    {
        return $this->isCLI ? $this->wrapCLI() : $this->wrapHTML();
    }
}
