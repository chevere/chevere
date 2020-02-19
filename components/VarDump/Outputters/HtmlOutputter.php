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

namespace Chevere\Components\VarDump\Outputters;

final class HtmlOutputter extends AbstractOutputter
{
    const BACKGROUND = '#132537';
    const BACKGROUND_SHADE = '#132537';
    /** @var string Dump style, no double quotes. */
    const STYLE = "font: 14px 'Fira Code Retina', 'Operator Mono', Inconsolata, Consolas,
    monospace, sans-serif, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: " . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';

    private bool $hasHeader = false;

    public function prepare(): void
    {
        if (headers_sent() === false || headers_list() === []) {
            $this->hasHeader = true;
            $this->writer()->write(
                '<html style="background: '
                . self::BACKGROUND_SHADE
                . ';"><head></head><body>'
            );
        }
        $this->writer()->write(
            implode('', [
                $this->caller(),
                '<hr>',
                '<pre style="' . self::STYLE . '">'
            ])
        );
    }

    public function callback(): void
    {
        $this->writer()->write('</pre>');
        if ($this->hasHeader) {
            $this->writer()->write('</body></html>');
        }
    }
}
