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

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Hooks\HooksRunner;
use Chevere\Components\Hooks\Tests\_resources\TestHookable;
use Chevere\Components\Hooks\Tests\_resources\TestHookableNoRegister;
use Chevere\Components\Hooks\Tests\_resources\TestHookableWithoutHooks;
use Chevere\Components\Plugs\Exceptions\PlugClassNotRegisteredException;
use Chevere\Components\Plugs\Plugs;
use PHPUnit\Framework\TestCase;

final class HookedTest extends TestCase
{
    private Plugs $plugs;

    public function setUp(): void
    {
        $resourcespath = (new Path(__DIR__ . '/'))
            ->getChild('_resources/HookedTest/');
        $hooksPath = (new Dir($resourcespath))->getChild('hooks-reg/')
            ->path()->absolute();
        $this->plugs = new Plugs(
            (new ClassMap)
                ->withPut(
                    'Chevere\Components\Hooks\Tests\_resources\TestHookable',
                    $hooksPath . 'Chevere/Components/Hooks/Tests/TestHookable/hooks.php'
                )
                ->withPut(
                    'Chevere\Components\Hooks\Tests\_resources\TestHookableWithCorruptedHooks',
                    $hooksPath . 'Chevere/Components/Hooks/Tests/MyHookableWithCorruptedHooks/hooks.php'
                )
                ->withPut(
                    'Chevere\Components\Hooks\Tests\_resources\TestHookableWithMissingHooks',
                    'error.php'
                )
        );
    }

    public function testWithoutHooksQueue(): void
    {
        $string = 'string';
        $testHookable = new TestHookableWithoutHooks;
        $testHookable->setString($string);
        $this->assertSame($string, $testHookable->string());
    }

    public function testHooked(): void
    {
        $string = 'string';
        $queue = $this->plugs->getQueue(TestHookable::class);
        /**
         * @var TestHookable $testHookable
         */
        $testHookable = (new TestHookable)
            ->withHooksRunner(
                new HooksRunner($queue)
            );
        $testHookable->setString($string);
        $this->assertSame("(hooked $string)", $testHookable->string());
    }

    public function testNotHookedClass(): void
    {
        $string = 'string';
        $testHookable = new TestHookableNoRegister();
        $testHookable->setString($string);
        $this->assertSame($string, $testHookable->string());
    }

    public function testClassNotRegistered(): void
    {
        $this->expectException(PlugClassNotRegisteredException::class);
        $this->plugs->getQueue(TestHookableNoRegister::class);
    }
}
