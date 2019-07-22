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

namespace Chevere\VarDumper;

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
    protected $isCli = false;

    /**
     * @param string $key color palette key
     */
    public function __construct(string $key, string $dump)
    {
        $this->key = $key;
        $this->dump = $dump;
    }

    public function useCli()
    {
        $this->consoleColor = new ConsoleColor();
        $this->isCli = true;
    }

    /**
     * TODO: toString interface.
     *
     * @return string color
     */
    public function toString(): string
    {
        return $this->wrap() ?? '';
    }

    protected function getCliColor(string $key): ?string
    {
        return Pallete::CONSOLE[$key] ?? null;
    }

    protected function getHtmlColor(string $key): ?string
    {
        return Pallete::PALETTE[$key] ?? null;
    }

    protected function wrapCli(): string
    {
        if ($color = $this->getCliColor($this->key)) {
            return $this->consoleColor->apply($color, $this->dump);
        }

        return $this->dump;
    }

    protected function wrapHtml()
    {
        if ($color = $this->getHtmlColor($this->key)) {
            return '<span style="color:' . $color . '">' . $this->dump . '</span>';
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
        return $this->isCli ? $this->wrapCli() : $this->wrapHtml();
    }
}
