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

use Chevere\Components\Extend\PluginsQueue;
use Chevere\Components\Hooks\Tests\_resources\TestHook;

return (new PluginsQueue)->withPlugin(new TestHook);
