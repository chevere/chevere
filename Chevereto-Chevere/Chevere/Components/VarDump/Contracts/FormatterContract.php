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

namespace Chevere\Components\VarDump\Contracts;

interface FormatterContract
{
    /**
     * @param int $indent Number of spaces to prefix
     */
    public function getIndent(int $indent): string;

    /**
     * @param string String to emphatize
     */
    public function applyEmphasis(string $string): string;

    /**
     * @param string String to encode its chars
     */
    public function filterEncodedChars(string $string): string;

    public function applyWrap(string $key, string $dump): string;
}
