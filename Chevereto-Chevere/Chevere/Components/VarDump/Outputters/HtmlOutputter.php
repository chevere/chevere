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

use Chevere\Components\VarDump\Interfaces\DumperInterface;

final class HtmlOutputter extends AbstractOutputter
{
    /**
     * {@inheritdoc}
     */
    public function prepare(string $output): string
    {
        if (false === headers_sent()) {
            $output .= '<html style="background: ' . DumperInterface::BACKGROUND_SHADE . ';"><head></head><body>';
        }
        $output .= '<pre style="' . DumperInterface::STYLE . '">';

        return $output;
    }
}
