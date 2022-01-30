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

namespace Chevere\Pluggable\Plug\Hook;

use Chevere\Pluggable\Interfaces\Plug\Hook\HooksRunnerInterface;

final class HooksRunnerNull implements HooksRunnerInterface
{
    public function run(string $anchor, &$argument): void
    {
        // empty
    }
}
