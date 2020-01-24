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

use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\TemplateInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use Chevere\Components\VarDump\Wrappers\HtmlWrapper;

/**
 * Provide HTML VarDump representation.
 */
final class HtmlFormatter implements FormatterInterface
{
    public function getIndent(int $indent): string
    {
        return str_repeat(TemplateInterface::HTML_INLINE_PREFIX, $indent);
    }

    public function applyEmphasis(string $string): string
    {
        return sprintf(
            TemplateInterface::HTML_EMPHASIS,
            (new HtmlWrapper(VarDumpInterface::_EMPHASIS))
                ->wrap($string)
        );
    }

    public function filterEncodedChars(string $string): string
    {
        return htmlspecialchars($string);
    }

    public function applyWrap(string $key, string $string): string
    {
        return
            (new HtmlWrapper($key))
                ->wrap($string);
    }
}
