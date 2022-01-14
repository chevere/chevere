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

namespace Chevere\Components\VarDump\Formatters;

use Chevere\Components\VarDump\Highlighters\VarDumpHtmlHighlight;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;

final class VarDumpHtmlFormatter implements VarDumpFormatterInterface
{
    public const HTML_INLINE_PREFIX = ' <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span>  ';

    public const HTML_EMPHASIS = '<em>%s</em>';

    public function indent(int $indent): string
    {
        return str_repeat(self::HTML_INLINE_PREFIX, $indent);
    }

    public function emphasis(string $string): string
    {
        return sprintf(
            self::HTML_EMPHASIS,
            (new VarDumpHtmlHighlight(VarDumperInterface::EMPHASIS))
                ->highlight($string)
        );
    }

    public function filterEncodedChars(string $string): string
    {
        return htmlspecialchars($string);
    }

    public function highlight(string $key, string $string): string
    {
        return (new VarDumpHtmlHighlight($key))->highlight($string);
    }
}
