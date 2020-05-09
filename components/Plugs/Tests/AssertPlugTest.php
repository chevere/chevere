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

use Chevere\Components\Hooks\Tests\_resources\TestHook;
use Chevere\Components\Plugs\AssertPlug;
use Chevere\Components\Plugs\Exceptions\PlugableNotExistsException;
use Chevere\Components\Plugs\Exceptions\PlugAnchorNotExistsException;
use Chevere\Components\Plugs\Tests\_resources\AssertPluginTest\TestHookClassNoInterface;
use Chevere\Components\Plugs\Tests\_resources\AssertPluginTest\TestHookClassNotFound;
use Chevere\Components\Plugs\Tests\_resources\AssertPluginTest\TestHookInvalidAnchor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AssertPlugTest extends TestCase
{
    public function testClassNotFound(): void
    {
        $hook = new TestHookClassNotFound;
        $this->expectException(PlugableNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testNoInterface(): void
    {
        $hook = new TestHookClassNoInterface;
        $this->expectException(InvalidArgumentException::class);
        new AssertPlug($hook);
    }

    public function testAnchorNotFound(): void
    {
        $hook = new TestHookInvalidAnchor;
        $this->expectException(PlugAnchorNotExistsException::class);
        new AssertPlug($hook);
    }

    public function testConstruct(): void
    {
        $hook = new TestHook;
        $assertHook = new AssertPlug($hook);
        $this->assertSame($hook, $assertHook->plug());
    }
}
