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

use Colors\Color;

final class ConsoleOutputter extends AbstractOutputter
{
    private string $outputHr;

    public function prepare(): void
    {
        $color = new Color;
        $this->outputHr = $color->fg('blue', str_repeat('-', 60));
        $this->writer()->write(
            implode("\n", [
                '',
                $color->fg('red', $color->bold($this->caller())),
                $this->outputHr,
            ])
        );
    }

    public function callback(): void
    {
        $this->writer()->write("\n" . $this->outputHr . "\n");
    }
}
