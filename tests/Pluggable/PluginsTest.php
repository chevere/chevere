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

namespace Chevere\Tests\Pluggable;

use Chevere\ClassMap\ClassMap;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Filesystem\Path;
use Chevere\Pluggable\Exceptions\PluggableNotRegisteredException;
use Chevere\Pluggable\Exceptions\PlugsFileNotExistsException;
use Chevere\Pluggable\Interfaces\PlugsQueueInterface;
use Chevere\Pluggable\Plugins;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;

final class PluginsTest extends TestCase
{
    private PathInterface $resourcesPath;

    protected function setUp(): void
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
        $this->assertEqualsCanonicalizing($classMap, $plugins->clonedClassMap());
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
        $map = $this->resourcesPath->getChild('invalid.php')->__toString();
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
        $map = $this->resourcesPath->getChild('corrupted.php')->__toString();
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
        $map = $this->resourcesPath->getChild('hooks.php')->__toString();
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
