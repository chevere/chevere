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

namespace Chevere\Components\Hooks\Traits;

use Chevere\Components\Hooks\Hooks;

trait HookTrait
{
    public function hook(string $anchor): void
    {
        $hooks = new Hooks($this, $anchor);
        // $hooks->withTrace();
        $hooks->exec();
    }
}
