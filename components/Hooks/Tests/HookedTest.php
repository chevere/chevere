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

use Chevere\Components\Hooks\Exceptions\HooksClassNotRegisteredException;
use Chevere\Components\Hooks\Exceptions\HooksFileNotFoundException;
use Chevere\Components\Hooks\Hooks;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Instances\HooksInstance;
use Go\ParserReflection\ReflectionFile;
use LogicException;
use PHPUnit\Framework\TestCase;

final class HookedTest extends TestCase
{
    private Hooks $hooks;

    public function setUp(): void
    {
        $this->hooks = new Hooks(include __DIR__ . '/_resources/hookables_classmap.php');
        new HooksInstance($this->hooks);
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
        // $reflectionFile = new ReflectionFile('/home/rodolfo/git/chevere/components/Hooks/Tests/MyHook.php');
        // $namespaces = $reflectionFile->getFileNamespaces();
        // foreach ($namespaces as $namespace) {
        //     $classes = $namespace->getClasses();
        //     foreach ($classes as $class) {
        //         xdd($class->getName(), $class->implementsInterface(HookInterface::class));
        //     }
        // }
        $string = 'string';
        $myHookable = new MyHookable();
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
        $this->hooks->queue(MyHookableWithNotRegisteredClass::class);
    }

    public function testHooksFileNotFound(): void
    {
        $this->expectException(HooksFileNotFoundException::class);
        new MyHookableWithMissingHooks();
    }

    public function testHooksFileCorrupted(): void
    {
        $this->expectException(LogicException::class);
        new MyHookableWithCorruptedHooks();
    }
}
