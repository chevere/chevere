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

use Chevere\Components\Pluggable\PlugsQueue;
use Chevere\Components\Pluggable\Types\HookPlugType;
use Chevere\Tests\Pluggable\Plugs\Hooks\_resources\TestHook;

return (new PlugsQueue(new HookPlugType()))
    ->withAdded(new TestHook());
