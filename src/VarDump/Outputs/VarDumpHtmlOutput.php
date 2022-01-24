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

namespace Chevere\VarDump\Outputs;

final class VarDumpHtmlOutput extends VarDumpAbstractOutput
{
    public const BACKGROUND = '#132537';

    public const BACKGROUND_SHADE = '#132537';

    /**
     * @var string Dump style, no double quotes.
     */
    public const STYLE = "font: 14px 'Fira Code Retina', 'Operator Mono', Inconsolata, Consolas, monospace, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: " . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';

    private bool $hasHeader = false;

    public function tearDown(): void
    {
        $this->writer()->write('</pre>');
        if ($this->hasHeader) {
            $this->writer()->write('</body></html>');
        }
    }

    public function prepare(): void
    {
        // @infection-ignore-all
        if (!headers_sent() || headers_list() === []) {
            $this->hasHeader = true;
            $this->writer()->write(
                '<html style="background: '
                . self::BACKGROUND_SHADE
                . ';"><head></head><body>'
            );
        }
        $this->writer()->write(
            implode('', [
                '<pre style="' . self::STYLE . '">',
                $this->caller(),
                '<hr style="opacity:.25">',
            ])
        );
    }
}
