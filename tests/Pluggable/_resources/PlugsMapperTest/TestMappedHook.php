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

namespace Chevere\Tests\Pluggable\_resources\PlugsMapperTest;

use Chevere\Pluggable\Interfaces\Plug\Hook\HookInterface;

final class TestMappedHook implements HookInterface
{
    public function __invoke(&$argument): void
    {
    }

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
}
