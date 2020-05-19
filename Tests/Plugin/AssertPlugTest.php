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

namespace Chevere\Tests\Plugin;

use Chevere\Components\Plugin\AssertPlug;
use Chevere\Components\Plugin\Exceptions\PluggableAnchorNotExistsException;
use Chevere\Components\Plugin\Exceptions\PluggableAnchorsException;
use Chevere\Components\Plugin\Exceptions\PluggableNotExistsException;
use Chevere\Components\Plugin\Exceptions\PlugInterfaceException;
use Chevere\Tests\Plugin\_resources\AssertPlugTest\TestHookAtInvalidInterface;
use Chevere\Tests\Plugin\_resources\AssertPlugTest\TestHookAtNotExists;
use Chevere\Tests\Plugin\_resources\AssertPlugTest\TestHookForNotExists;
use Chevere\Tests\Plugin\_resources\AssertPlugTest\TestUnacceptedPlug;
use Chevere\Tests\Plugin\_resources\src\TestHook;
use Chevere\Components\Plugin\Types\HookPlugType;
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
