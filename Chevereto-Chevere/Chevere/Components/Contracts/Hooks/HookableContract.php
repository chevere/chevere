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

namespace Chevere\Contracts\Hooks;

interface HookableContract
{
    /**
     * Register a hookable entry. Hooks (if any) will run in this point.
     *
     * @param string $anchor Named anchor for the hook
     */
    public function hook(string $anchor): void;
}
