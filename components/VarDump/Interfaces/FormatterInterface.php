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

namespace Chevere\Components\VarDump\Interfaces;

interface FormatterInterface
{
    /**
     * @param int $indent Number of spaces to prefix
     */
    public function indent(int $indent): string;

    /**
     * @param string $string String to emphatize
     */
    public function emphasis(string $string): string;

    /**
     * @param string $string String to encode its chars
     */
    public function filterEncodedChars(string $string): string;

    /**
     * @param string $key A highlight key, from HighlightInterface::KEYS
     * @param string $string String to highlight
     */
    public function highlight(string $key, string $string): string;
}
