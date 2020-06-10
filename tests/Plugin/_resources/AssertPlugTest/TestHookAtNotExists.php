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

namespace Chevere\Tests\Plugin\_resources\AssertPlugTest;

use Chevere\Tests\Plugin\_resources\src\TestHook;

final class TestHookAtNotExists extends TestHook
{
    public function at(): string
    {
        return uniqid();
    }
}
