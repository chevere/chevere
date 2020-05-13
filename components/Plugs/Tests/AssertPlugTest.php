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

use Chevere\Components\Plugs\AssertPlug;
use Chevere\Components\Plugs\Exceptions\PluggableAnchorNotExistsException;
use Chevere\Components\Plugs\Exceptions\PluggableAnchorsException;
use Chevere\Components\Plugs\Exceptions\PluggableNotExistsException;
use Chevere\Components\Plugs\Exceptions\PlugInterfaceException;
use Chevere\Components\Plugs\Tests\_resources\AssertPlugTest\TestHookAtInvalidInterface;
use Chevere\Components\Plugs\Tests\_resources\AssertPlugTest\TestHookAtNotExists;
use Chevere\Components\Plugs\Tests\_resources\AssertPlugTest\TestHookForNotExists;
use Chevere\Components\Plugs\Tests\_resources\AssertPlugTest\TestUnacceptedPlug;
use Chevere\Components\Plugs\Tests\_resources\src\TestHook;
use Chevere\Components\Plugs\Types\HookPlugType;
use PHPUnit\Framework\TestCase;

final class AssertPlugTest extends TestCase
{
    public function testUnaccepted(): void
    {
        $plug = new TestUnacceptedPlug;
        $this->expectException(PlugInterfaceException::class);
        new AssertPlug($plug);
    }

    public function testAtNotExists(): void
    {
        $hook = new TestHookAtNotExists;
        $this->expectException(PluggableNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testAtInvalidInterface(): void
    {
        $hook = new TestHookAtInvalidInterface;
        $this->expectException(PluggableAnchorsException::class);
        new AssertPlug($hook);
    }

    public function testForNotExists(): void
    {
        $hook = new TestHookForNotExists;
        $this->expectException(PluggableAnchorNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testConstruct(): void
    {
        $hook = new TestHook;
        $assertHook = new AssertPlug($hook);
        $this->assertSame($hook, $assertHook->plug());
        $this->assertEquals(new HookPlugType, $assertHook->type());
    }
}
