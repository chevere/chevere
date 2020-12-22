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

namespace Chevere\Tests\Pluggable;

use Chevere\Components\Pluggable\AssertPlug;
use Chevere\Components\Pluggable\Types\HookPlugType;
use Chevere\Exceptions\Pluggable\PluggableAnchorNotExistsException;
use Chevere\Exceptions\Pluggable\PluggableAnchorsException;
use Chevere\Exceptions\Pluggable\PluggableNotExistsException;
use Chevere\Exceptions\Pluggable\PlugInterfaceException;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestHookAtInvalidInterface;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestHookAtNotExists;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestHookForNotExists;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestUnacceptedPlug;
use Chevere\Tests\Pluggable\_resources\src\TestHook;
use PHPUnit\Framework\TestCase;

final class AssertPlugTest extends TestCase
{
    public function testUnaccepted(): void
    {
        $plug = new TestUnacceptedPlug();
        $this->expectException(PlugInterfaceException::class);
        new AssertPlug($plug);
    }

    public function testAtNotExists(): void
    {
        $hook = new TestHookAtNotExists();
        $this->expectException(PluggableNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testAtInvalidInterface(): void
    {
        $hook = new TestHookAtInvalidInterface();
        $this->expectException(PluggableAnchorsException::class);
        new AssertPlug($hook);
    }

    public function testForNotExists(): void
    {
        $hook = new TestHookForNotExists();
        $this->expectException(PluggableAnchorNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testConstruct(): void
    {
        $hook = new TestHook();
        $assertHook = new AssertPlug($hook);
        $this->assertSame($hook, $assertHook->plug());
        $this->assertInstanceOf(HookPlugType::class, $assertHook->plugType());
    }
}
