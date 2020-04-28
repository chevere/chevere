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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Hooks\Exceptions\HooksClassNotRegisteredException;
use Chevere\Components\Hooks\HookablesMap;
use Chevere\Components\Hooks\Hooks;
use PHPUnit\Framework\TestCase;

final class HookedTest extends TestCase
{
    private Hooks $hooks;

    public function setUp(): void
    {
        $resourcespath = (new Path(__DIR__ . '/'))->getChild('_resources/');
        $hooksPath = (new Dir($resourcespath))->getChild('hooks/')->path()->absolute();
        $this->hooks = new Hooks(
            (new HookablesMap)
                ->withPut(
                    'Chevere\Components\Hooks\Tests\MyHookable',
                    $hooksPath . 'Chevere/Components/Hooks/Tests/MyHookable/hooks.php'
                )
                ->withPut(
                    'Chevere\Components\Hooks\Tests\MyHookableWithCorruptedHooks',
                    $hooksPath . 'Chevere/Components/Hooks/Tests/MyHookableWithCorruptedHooks/hooks.php'
                )
                ->withPut(
                    'Chevere\Components\Hooks\Tests\MyHookableWithMissingHooks',
                    'error.php'
                )
        );

        include_once 'MyHookable.php';
    }

    public function testWithoutHooksQueue(): void
    {
        $string = 'string';
        $myHookable = new MyHookableWithoutHooks();
        $myHookable->setString($string);
        $this->assertSame($string, $myHookable->string());
    }

    public function testHooked(): void
    {
        $string = 'string';
        /**
         * @var MyHookable $myHookable
         */
        $myHookable = (new MyHookable)
            ->withHooksRunner($this->hooks->getRunner(MyHookable::class));
        $myHookable->setString($string);
        $this->assertSame("(hooked $string)", $myHookable->string());
    }

    public function testNotHookedClass(): void
    {
        $string = 'string';
        $myHookable = new MyHookableWithNotRegisteredClass();
        $myHookable->setString($string);
        $this->assertSame($string, $myHookable->string());
    }

    public function testClassNotRegistered(): void
    {
        $this->expectException(HooksClassNotRegisteredException::class);
        $this->hooks->getRunner(MyHookableWithNotRegisteredClass::class);
    }
}
