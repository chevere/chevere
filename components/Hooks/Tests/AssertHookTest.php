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
use PHPUnit\Framework\TestCase;

final class AssertHookTest extends TestCase
{
    public function testClassNotFound(): void
    {
        $hook = new MyHookClassNotFound();
        $this->expectException(HookableNotFoundException::class);
        new AssertHook($hook);
    }

    public function testNoInterface(): void
    {
        $hook = new MyHookClassNoInterface();
        $this->expectException(HookableInterfaceException::class);
        new AssertHook($hook);
    }

    public function testAnchorNotFound(): void
    {
        $hook = new MyHookInvalidAnchor();
        $this->expectException(AnchorNotFoundException::class);
        new AssertHook($hook);
    }
}

final class MyHookClassNotFound extends MyHook
{
    public static function hookableClassName(): string
    {
        return uniqid();
    }
}

final class MyHookClassNoInterface extends MyHook
{
    public static function hookableClassName(): string
    {
        return Path::class;
    }
}

final class MyHookInvalidAnchor extends MyHook
{
    public static function anchor(): string
    {
        return uniqid();
    }
}
