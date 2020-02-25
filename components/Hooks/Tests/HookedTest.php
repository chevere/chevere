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
        $resourcespath = (new Path(__DIR__))->getChild('_resources');
        $hookablesClassmap = $resourcespath->getChild('hookables_classmap.php');
        $hookables = include $hookablesClassmap->absolute();
        foreach ($hookables as $k => &$v) {
            $v = strtr($v, [
                '%hooksPath%' => (new Dir($resourcespath))->getChild('hooks')
                    ->path()->absolute()
            ]);
        }
        $this->hooks = new Hooks($hookables);
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
