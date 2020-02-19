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

use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleOutputter extends AbstractOutputter
{
    private string $outputHr;

    public function prepare(): void
    {
        $this->outputHr = (new ConsoleColor)->apply(
            'blue',
            '------------------------------------------------------------'
        );
        $this->writer()->write(
            implode("\n", [
                '',
                (new ConsoleColor)->apply(['bold', 'red'], $this->caller()),
                $this->outputHr,
                ''
            ])
        );
    }

    public function callback(): void
    {
        $this->writer()->write("\n" . $this->outputHr . "\n");
    }
}
