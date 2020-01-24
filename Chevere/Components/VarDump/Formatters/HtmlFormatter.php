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
use Chevere\Components\VarDump\Interfaces\VarInfoInterface;
use Chevere\Components\VarDump\Wrappers\HtmlHighlight;

/**
 * Provide HTML VarDump representation.
 */
final class HtmlFormatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function indent(int $indent): string
    {
        return str_repeat(TemplateInterface::HTML_INLINE_PREFIX, $indent);
    }

    /**
     * {@inheritdoc}
     */
    public function emphasis(string $string): string
    {
        return sprintf(
            TemplateInterface::HTML_EMPHASIS,
            (new HtmlHighlight(VarInfoInterface::_EMPHASIS))
                ->wrap($string)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function filterEncodedChars(string $string): string
    {
        return htmlspecialchars($string);
    }

    /**
     * {@inheritdoc}
     */
    public function highlight(string $key, string $string): string
    {
        return
            (new HtmlHighlight($key))
                ->wrap($string);
    }
}
