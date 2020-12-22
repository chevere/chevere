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

namespace Chevere\Tests\Pluggable\_resources\AssertPlugTest;

use Chevere\Tests\Pluggable\_resources\src\TestHook;

final class TestHookAtInvalidInterface extends TestHook
{
    public function at(): string
    {
        return TestHook::class;
    }
}
