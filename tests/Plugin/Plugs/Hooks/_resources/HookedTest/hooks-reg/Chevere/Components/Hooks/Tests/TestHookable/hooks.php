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

use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Tests\Plugin\Plugs\Hooks\_resources\TestHook;

return (new PlugsQueue(new HookPlugType()))
    ->withAdded(new TestHook());
