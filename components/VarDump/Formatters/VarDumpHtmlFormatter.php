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
use Chevere\Interfaces\VarDump\VarDumpTemplateInterface;

final class VarDumpHtmlFormatter implements VarDumpFormatterInterface
{
    public function indent(int $indent): string
    {
        return str_repeat(VarDumpTemplateInterface::HTML_INLINE_PREFIX, $indent);
    }

    public function emphasis(string $string): string
    {
        return sprintf(
            VarDumpTemplateInterface::HTML_EMPHASIS,
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
        return
            (new VarDumpHtmlHighlight($key))
                ->highlight($string);
    }
}
