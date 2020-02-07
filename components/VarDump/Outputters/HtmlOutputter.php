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
use function ChevereFn\stringStartsWith;

final class HtmlOutputter extends PlainOutputter
{
    public function prepare(string $output): string
    {
        if (headers_sent() === false || headers_list() === []) {
            $output .= '<html style="background: ' . VarDumperInterface::BACKGROUND_SHADE . ';"><head></head><body>';
        }
        $output .= '<pre style="' . VarDumperInterface::STYLE . '">';

        return $output;
    }

    public function callback(string $output): string
    {
        if (stringStartsWith('<html', $output)) {
            $output .= '</body></html>';
        }

        return $output;
    }
}
