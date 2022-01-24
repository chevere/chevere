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

use Chevere\Pluggable\AssertPlug;
use Chevere\Pluggable\Types\HookPlugType;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestHookAtInvalidInterface;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestHookAtNotExists;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestHookForNotExists;
use Chevere\Tests\Pluggable\_resources\AssertPlugTest\TestUnacceptedPlug;
use Chevere\Tests\Pluggable\_resources\src\TestHook;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\ClassNotExistsException;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;

final class AssertPlugTest extends TestCase
{
    public function testUnaccepted(): void
    {
        $plug = new TestUnacceptedPlug();
        $this->expectException(TypeError::class);
        new AssertPlug($plug);
    }

    public function testAtNotExists(): void
    {
        $hook = new TestHookAtNotExists();
        $this->expectException(ClassNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testAtInvalidInterface(): void
    {
        $hook = new TestHookAtInvalidInterface();
        $this->expectException(LogicException::class);
        new AssertPlug($hook);
    }

    public function testForNotExists(): void
    {
        $hook = new TestHookForNotExists();
        $this->expectException(LogicException::class);
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
