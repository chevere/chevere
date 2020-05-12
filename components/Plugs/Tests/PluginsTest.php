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

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\ExceptionHandler\Exceptions\RuntimeException;
use Chevere\Components\Filesystem\Interfaces\PathInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Plugs\Exceptions\PlugableNotRegisteredException;
use Chevere\Components\Plugs\Exceptions\PlugsFileNotExistsException;
use Chevere\Components\Plugs\Exceptions\PlugsQueueInterfaceException;
use Chevere\Components\Plugs\Interfaces\PlugsQueueInterface;
use Chevere\Components\Plugs\Plugins;
use PHPUnit\Framework\TestCase;

final class PluginsTest extends TestCase
{
    private PathInterface $resourcesPath;

    public function setUp(): void
    {
        $this->resourcesPath = new Path(
            (__DIR__) . '/_resources/PluginsTest/'
        );
    }

    public function testEmpty(): void
    {
        $classMap = new ClassMap;
        $plugins = new Plugins($classMap);
        $this->assertNotSame($classMap, $plugins->classMap());
        $this->assertEquals($classMap, $plugins->classMap());
        $plugable = 'notRegistered';
        $this->expectException(PlugableNotRegisteredException::class);
        $plugins->getPlugsQueue($plugable);
    }

    public function testRegisteredNotExists(): void
    {
        $plugable = 'registered';
        $map = uniqid() . '.php';
        $plugins = new Plugins(
            (new ClassMap)->withPut($plugable, $map)
        );
        $this->expectException(PlugsFileNotExistsException::class);
        $plugins->getPlugsQueue($plugable);
    }

    public function testRegisteredWrongReturnType(): void
    {
        $plugable = 'registered';
        $map = $this->resourcesPath->getChild('invalid.php')->absolute();
        $plugins = new Plugins(
            (new ClassMap)->withPut($plugable, $map)
        );
        $this->expectException(PlugsQueueInterfaceException::class);
        $plugins->getPlugsQueue($plugable);
    }

    public function testRegisteredCorrupted(): void
    {
        $plugable = 'registered';
        $map = $this->resourcesPath->getChild('corrupted.php')->absolute();
        $plugins = new Plugins(
            (new ClassMap)->withPut($plugable, $map)
        );
        $this->expectException(RuntimeException::class);
        $plugins->getPlugsQueue($plugable);
    }

    public function testRegisteredHooks(): void
    {
        $plugable = 'registered';
        $map = $this->resourcesPath->getChild('hooks.php')->absolute();
        $plugins = new Plugins(
            (new ClassMap)->withPut($plugable, $map)
        );
        $this->assertInstanceOf(
            PlugsQueueInterface::class,
            $plugins->getPlugsQueue($plugable)
        );
    }
}
