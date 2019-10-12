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

namespace Chevere\VarDump\src;

use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Wraps dump data into colored output for CLI and HTML.
 */
final class Wrapper
{
    /** @var string */
    private $key;

    /** @var string */
    private $dump;

    /** @var ConsoleColor */
    private $consoleColor;

    /** @var bool */
    private $useCli;

    /**
     * @param string $key color palette key
     * @param string $dump the dump expresion
     */
    public function __construct(string $key, string $dump)
    {
        $this->key = $key;
        $this->dump = $dump;
        $this->useCli =  false;
    }

    public function withCli(): Wrapper
    {
        $new = clone $this;
        $new->consoleColor = new ConsoleColor();
        $new->useCli = true;
        return $new;
    }

    /**
     * @return string wrapped colored string
     */
    public function toString(): string
    {
        return $this->wrap();
    }

    private function getCliColor(string $key): ?string
    {
        return Pallete::CONSOLE[$key] ?? null;
    }

    private function getHtmlColor(string $key): ?string
    {
        return Pallete::PALETTE[$key] ?? null;
    }

    private function wrapCli(): string
    {
        $color = $this->getCliColor($this->key);
        if (isset($color)) {
            return $this->consoleColor->apply($color, $this->dump);
        }

        return $this->dump;
    }

    private function wrapHtml()
    {
        $color = $this->getHtmlColor($this->key);
        if ($color) {
            return '<span style="color:' . $color . '">' . $this->dump . '</span>';
        }

        return $this->dump;
    }

    /**
     * Wrap dump data.
     *
     * @return string wrapped dump data
     */
    private function wrap(): string
    {
        return $this->useCli ? $this->wrapCli() : $this->wrapHtml();
    }
}
