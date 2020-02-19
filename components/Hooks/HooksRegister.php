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

namespace Chevere\Components\Hooks;

final class HooksRegister
{
    public function add(
        string $className,
        string $anchor,
        int $priority,
        string $file
    ) {
    }
}

// $registerHooks = new HooksRegister();
// $registerHooks->add('className', 'anchor', 0, )
