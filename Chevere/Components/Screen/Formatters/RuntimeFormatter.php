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

namespace Chevere\Components\Screen\Formatters;

use Chevere\Components\Screen\Interfaces\FormatterInterface;

final class RuntimeFormatter implements FormatterInterface
{
    public function wrap(string $display): string
    {
        return $display;
    }
}
