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

namespace Chevere\Components\Plugs\Tests;

use Chevere\Components\Hooks\Exceptions\AnchorNotFoundException;
use Chevere\Components\Hooks\Exceptions\HookableInterfaceException;
use Chevere\Components\Hooks\Exceptions\HookableNotFoundException;
use Chevere\Components\Hooks\Tests\_resources\TestHook;
use Chevere\Components\Plugs\AssertPlug;
use Chevere\Components\Plugs\Tests\_resources\AssertPluginTest\TestHookClassNoInterface;
use Chevere\Components\Plugs\Tests\_resources\AssertPluginTest\TestHookClassNotFound;
use Chevere\Components\Plugs\Tests\_resources\AssertPluginTest\TestHookInvalidAnchor;
use PHPUnit\Framework\TestCase;

final class AssertPluginTest extends TestCase
{
    public function testClassNotFound(): void
    {
        $hook = new TestHookClassNotFound;
        $this->expectException(HookableNotFoundException::class);
        new AssertPlug($hook);
    }

    public function testNoInterface(): void
    {
        $hook = new TestHookClassNoInterface;
        $this->expectException(HookableInterfaceException::class);
        new AssertPlug($hook);
    }

    public function testAnchorNotFound(): void
    {
        $hook = new TestHookInvalidAnchor;
        $this->expectException(AnchorNotFoundException::class);
        new AssertPlug($hook);
    }

    public function testConstruct(): void
    {
        $hook = new TestHook;
        $assertHook = new AssertPlug($hook);
        $this->assertSame($hook, $assertHook->plug());
    }
}
