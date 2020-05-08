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

namespace Chevere\Components\Extend\Tests\_resources\AssertPluginTest;

use Chevere\Components\Hooks\Tests\_resources\HooksIteratorTest\hooks\TestHook;

final class TestHookClassNoInterface extends TestHook
{
    public function at(): string
    {
        return TestHook::class;
    }
}
