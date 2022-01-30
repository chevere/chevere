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

namespace Chevere\VarDump\Interfaces;

/**
 * Describes the component in charge of formatting the var dump strings.
 */
interface VarDumpFormatInterface
{
    /**
     * Get indent for the given `$indent` size.
     */
    public function indent(int $indent): string;

    /**
     * Get emphasis for the given `$string`.
     */
    public function emphasis(string $string): string;

    /**
     * Get `$string` without encoded chars.
     */
    public function filterEncodedChars(string $string): string;

    /**
     * Get highlighted `$string` identified by `$key`.
     *
     * @see VarDumpHighlightInterface
     */
    public function highlight(string $key, string $string): string;
}
