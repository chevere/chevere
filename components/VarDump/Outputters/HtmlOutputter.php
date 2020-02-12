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

use Chevere\Components\VarDump\Interfaces\VarDumperInterface;

final class HtmlOutputter extends PlainOutputter
{
    private bool $hasHeader = false;

    public function prepare(): void
    {
        if (headers_sent() === false || headers_list() === []) {
            $this->hasHeader = true;
            $this->streamWriter->write(
                '<html style="background: '
                . VarDumperInterface::BACKGROUND_SHADE
                . ';"><head></head><body>'
            );
        }
        $this->streamWriter->write(
            '<pre style="' . VarDumperInterface::STYLE . '">'
        );
    }

    public function callback(): void
    {
        if ($this->hasHeader) {
            $this->streamWriter->write('</body></html>');
        }
    }
}
