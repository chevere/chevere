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

namespace Chevere\Components\Plugs\Tests\_resources\PlugsMapperTest;

use Chevere\Components\Hooks\Interfaces\HookInterface;

final class TestMappedHook implements HookInterface
{
    public function anchor(): string
    {
        return 'hook-anchor-1';
    }

    public function at(): string
    {
        return TestMappedHookable::class;
    }

    public function priority(): int
    {
        return 0;
    }

    public function __invoke(&$argument): void
    {
    }
}
