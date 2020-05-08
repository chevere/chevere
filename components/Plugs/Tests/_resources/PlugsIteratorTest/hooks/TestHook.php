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

namespace Chevere\Components\Plugs\Tests\_resources\PlugsIteratorTest\hooks;

use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Plugs\Tests\_resources\PlugsIteratorTest\hookables\TestHookable;

class TestHook implements HookInterface
{
    public function for(): string
    {
        return 'setString:after';
    }

    public function at(): string
    {
        return TestHookable::class;
    }

    public function priority(): int
    {
        return 0;
    }

    public function __invoke(&$argument): void
    {
        $argument = "(hooked $argument)";
    }
}
