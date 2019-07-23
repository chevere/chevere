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

namespace Chevere\VarDump;

/**
 * Analyze a variable and provide a HTML output string representation of its type and data.
 */
class HtmlVarDump extends VarDump
{
    protected function setPrefix(): void
    {
        $this->prefix = str_repeat(Template::HTML_INLINE_PREFIX, $this->indent);
    }

    protected function getEmphasis(string $string): string
    {
        return sprintf(Template::HTML_EMPHASIS, $string);
    }

    protected function filterChars(string $string): string
    {
        return htmlspecialchars($string);
    }

    public static function wrap(string $key, string $dump): ?string
    {
        $wrapper = new Wrapper($key, $dump);

        return $wrapper->toString();
    }
}
