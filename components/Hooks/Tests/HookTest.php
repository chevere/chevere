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

use Chevere\Components\Hooks\Hooks;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Instances\HooksInstance;
use Chevere\Components\Stopwatch\Stopwatch;
use Go\ParserReflection\ReflectionFile;
use PHPUnit\Framework\TestCase;

final class HookTest extends TestCase
{
    public function testConstruct(): void
    {
        // $reflectionFile = new ReflectionFile('/home/rodolfo/git/chevere/components/Hooks/Tests/MyHook.php');
        // $namespaces = $reflectionFile->getFileNamespaces();
        // foreach ($namespaces as $namespace) {
        //     $classes = $namespace->getClasses();
        //     foreach ($classes as $class) {
        //         xdd($class->getName(), $class->implementsInterface(HookInterface::class));
        //     }
        // }
        new HooksInstance(
            new Hooks(include __DIR__ . '/_resources/hookables_classmap.php')
        );
        $string = 'string';
        $myHookable = new MyHookable();
        $myHookable->setString($string);
        $this->assertSame("(hooked $string)", $myHookable->string());
    }
}
