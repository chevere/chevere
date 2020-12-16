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

namespace Chevere\Tests\Plugin;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Plugin\Plugins;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Plugin\PluggableNotRegisteredException;
use Chevere\Exceptions\Plugin\PlugsFileNotExistsException;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
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
        $classMap = new ClassMap();
        $plugins = new Plugins($classMap);
        $this->assertNotSame($classMap, $plugins->clonedClassMap());
        $this->assertEquals($classMap, $plugins->clonedClassMap());
        $pluggable = 'notRegistered';
        $this->expectException(PluggableNotRegisteredException::class);
        $plugins->getPlugsQueue($pluggable);
    }

    public function testRegisteredNotExists(): void
    {
        $pluggable = 'stdClass';
        $map = uniqid() . '.php';
        $plugins = new Plugins(
            (new ClassMap())
                ->withPut($pluggable, $map)
        );
        $this->expectException(PlugsFileNotExistsException::class);
        $plugins->getPlugsQueue($pluggable);
    }

    public function testRegisteredWrongReturnType(): void
    {
        $pluggable = 'stdClass';
        $map = $this->resourcesPath->getChild('invalid.php')->absolute();
        $plugins = new Plugins(
            (new ClassMap())
                ->withPut($pluggable, $map)
        );
        $this->expectException(LogicException::class);
        $plugins->getPlugsQueue($pluggable);
    }

    public function testRegisteredCorrupted(): void
    {
        $pluggable = 'stdClass';
        $map = $this->resourcesPath->getChild('corrupted.php')->absolute();
        $plugins = new Plugins(
            (new ClassMap())
                ->withPut($pluggable, $map)
        );
        $this->expectException(LogicException::class);
        $plugins->getPlugsQueue($pluggable);
    }

    public function testRegisteredHooks(): void
    {
        $pluggable = 'stdClass';
        $map = $this->resourcesPath->getChild('hooks.php')->absolute();
        $plugins = new Plugins(
            (new ClassMap())
                ->withPut($pluggable, $map)
        );
        $this->assertInstanceOf(
            PlugsQueueInterface::class,
            $plugins->getPlugsQueue($pluggable)
        );
    }
}
