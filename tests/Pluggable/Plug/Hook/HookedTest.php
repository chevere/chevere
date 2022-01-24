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

namespace Chevere\Tests\Pluggable\Plug\Hook;

use Chevere\ClassMap\ClassMap;
use Chevere\Filesystem\Dir;
use Chevere\Filesystem\Path;
use Chevere\Pluggable\Interfaces\PluginsInterface;
use Chevere\Pluggable\Plug\Hook\HooksQueue;
use Chevere\Pluggable\Plug\Hook\HooksRunner;
use Chevere\Pluggable\Plugins;
use Chevere\Tests\Pluggable\Plug\Hook\_resources\TestHook;
use Chevere\Tests\Pluggable\Plug\Hook\_resources\TestHookable;
use Chevere\Tests\Pluggable\Plug\Hook\_resources\TestHookableWithoutHooks;
use PHPUnit\Framework\TestCase;

final class HookedTest extends TestCase
{
    private PluginsInterface $plugins;

    protected function setUp(): void
    {
        $resources = (new Path(__DIR__ . '/_resources/'));
        $hooksPath = (new Dir($resources))->getChild('HookedTest/hooks-reg/')
            ->path()->__toString();
        $srcAt = $resources->__toString();
        $nsHookable = 'Chevere\Tests\Pluggable\Plug\Hook\_resources';
        $fsHooks = 'Chevere/Components/Hooks/Tests/';
        $classMap = new ClassMap();
        foreach ([
            'TestHookable' => $hooksPath . "${fsHooks}/TestHookable/hooks.php",
            'TestHookableWithCorruptedHooks' => $hooksPath . "${fsHooks}/MyHookableWithCorruptedHooks/hooks.php",
            'TestHookableWithMissingHooks' => 'error.php',
        ] as $name => $path) {
            require_once $srcAt . $name . '.php';
            $fqn = "${nsHookable}\\${name}";
            $classMap = $classMap->withPut($fqn, $path);
        }
        $this->plugins = new Plugins($classMap);
    }

    public function testWithoutHooksQueue(): void
    {
        $string = 'string';
        $testHookable = new TestHookableWithoutHooks();
        $testHookable->setString($string);
        $this->assertSame($string, $testHookable->string());
    }

    public function testHooked(): void
    {
        $string = 'string';
        $hooksQueue = (new HooksQueue())->withAdded(new TestHook());
        /** @var TestHookable $testHookable */
        $testHookable = (new TestHookable())
            ->withHooksRunner(
                new HooksRunner($hooksQueue)
            );
        $testHookable->setString($string);
        $this->assertSame("(hooked ${string})", $testHookable->string());
    }

    public function testNotHookedClass(): void
    {
        $string = 'string';
        $testHookable = new TestHookable();
        $testHookable->setString($string);
        $this->assertSame($string, $testHookable->string());
    }
}
