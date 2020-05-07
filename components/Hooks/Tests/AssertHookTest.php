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

namespace Chevere\Components\Hooks\Tests;

use Chevere\Components\Filesystem\Path;
use Chevere\Components\Hooks\AssertHook;
use Chevere\Components\Hooks\Exceptions\AnchorNotFoundException;
use Chevere\Components\Hooks\Exceptions\HookableInterfaceException;
use Chevere\Components\Hooks\Exceptions\HookableNotFoundException;
use Chevere\Components\Hooks\Tests\_resources\AssertHookTest\TestHookClassNoInterface;
use Chevere\Components\Hooks\Tests\_resources\AssertHookTest\TestHookClassNotFound;
use Chevere\Components\Hooks\Tests\_resources\AssertHookTest\TestHookInvalidAnchor;
use Chevere\Components\Hooks\Tests\_resources\TestHook;
use PHPUnit\Framework\TestCase;

final class AssertHookTest extends TestCase
{
    public function testClassNotFound(): void
    {
        $hook = new TestHookClassNotFound;
        $this->expectException(HookableNotFoundException::class);
        new AssertHook($hook);
    }

    public function testNoInterface(): void
    {
        $hook = new TestHookClassNoInterface;
        $this->expectException(HookableInterfaceException::class);
        new AssertHook($hook);
    }

    public function testAnchorNotFound(): void
    {
        $hook = new TestHookInvalidAnchor;
        $this->expectException(AnchorNotFoundException::class);
        new AssertHook($hook);
    }

    public function testConstruct(): void
    {
        $hook = new TestHook;
        $assertHook = new AssertHook($hook);
        $this->assertSame($hook, $assertHook->hook());
    }
}
