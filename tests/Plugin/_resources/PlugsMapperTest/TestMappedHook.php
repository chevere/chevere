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

namespace Chevere\Tests\Plugin\_resources\PlugsMapperTest;

use Chevere\Interfaces\Plugin\Plugs\Hooks\HookInterface;

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
