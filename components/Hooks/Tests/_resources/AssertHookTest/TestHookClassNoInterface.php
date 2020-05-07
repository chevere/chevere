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

namespace Chevere\Components\Hooks\Tests\_resources\AssertHookTest;

use Chevere\Components\Hooks\Tests\_resources\HooksIteratorTest\hooks\TestHook;

final class TestHookClassNoInterface extends TestHook
{
    public function className(): string
    {
        return TestHook::class;
    }
}
