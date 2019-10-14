<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Hook\Traits;

use Chevere\Hook\Hooks;

trait HookTrait
{
    public function hook(string $anchor): void
    {
        $hooks = new Hooks();
        // $hooks->withTrace();
        $hooks->exec($anchor, $this);
    }
}
