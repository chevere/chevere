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

namespace Chevere\Components\Screen;

use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleScreen extends AbstractScreen
{
    protected function wrap(string $display): string
    {
        $char = (new ConsoleColor)->apply('reverse', '%');

        return $char . $display . $char;
    }
}
