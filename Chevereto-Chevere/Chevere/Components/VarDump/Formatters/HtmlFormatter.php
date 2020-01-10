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

namespace Chevere\Components\VarDump\Formatters;

use Chevere\Components\VarDump\src\Template;
use Chevere\Components\VarDump\src\Wrapper;
use Chevere\Components\VarDump\Contracts\FormatterContract;

/**
 * Provide HTML VarDump representation.
 */
final class HtmlFormatter implements FormatterContract
{
    public function getIndent(int $indent): string
    {
        return str_repeat(Template::HTML_INLINE_PREFIX, $indent);
    }

    public function getEmphasis(string $string): string
    {
        return sprintf(Template::HTML_EMPHASIS, $string);
    }

    public function getEncodedChars(string $string): string
    {
        return htmlspecialchars($string);
    }

    public function getWrap(string $key, string $dump): string
    {
        $wrapper = new Wrapper($key, $dump);

        return $wrapper->toString();
    }
}
