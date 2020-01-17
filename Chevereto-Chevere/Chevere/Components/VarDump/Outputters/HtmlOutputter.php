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

use Chevere\Components\VarDump\Interfaces\OutputterInterface;

final class HtmlOutputter extends AbstractOutputter
{
    /**
     * {@inheritdoc}
     */
    public function prepare(): OutputterInterface
    {
        if (false === headers_sent()) {
            $this->output .= '<html style="background: ' . $this->dumper::BACKGROUND_SHADE . ';"><head></head><body>';
        }
        $this->output .= '<pre style="' . $this->dumper::STYLE . '">';

        return $this;
    }

    public function print(): void
    {
        echo $this->output;
    }
}
