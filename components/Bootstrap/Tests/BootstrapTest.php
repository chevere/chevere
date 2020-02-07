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

namespace Chevere\Components\Bootstrap\Tests;

use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Bootstrap\Exceptions\BootstrapDirException;
use Chevere\Components\Bootstrap\Interfaces\BootstrapInterface;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    private function getRootDir(): DirInterface
    {
        return (new Dir(new Path(__DIR__)))->getChild('resources/root');
    }

    private function getAppDir(DirInterface $rootDir): DirInterface
    {
        return $rootDir->getChild('app');
    }

    private function getBootstrap(): BootstrapInterface
    {
        $rootDir = $this->getRootDir();

        return new Bootstrap($rootDir, $this->getAppDir($rootDir));
    }

    public function testConstruct(): void
    {
        $rootDir = $this->getRootDir();
        $appDir = $this->getAppDir($rootDir);
        $bootstrap = new Bootstrap($rootDir, $appDir);
        $this->assertSame($rootDir, $bootstrap->rootDir());
        $this->assertSame($appDir, $bootstrap->appDir());
        $this->assertIsInt($bootstrap->time());
        $this->assertIsInt($bootstrap->hrtime());
        $this->assertFalse($bootstrap->isCli());
        $this->assertFalse($bootstrap->isDev());
        $this->assertFalse($bootstrap->hasConsole());
    }

    public function testWithNonExistentDirs(): void
    {
        $rootDir = $this->getRootDir();
        $appDir = $this->getAppDir($rootDir)->getChild(uniqid());
        $this->expectException(BootstrapDirException::class);
        new Bootstrap($rootDir, $appDir);
    }

    public function testWithCli(): void
    {
        $bootstrap = $this->getBootstrap()->withCli(true);
        $this->assertTrue($bootstrap->isCli());
        $bootstrap = $bootstrap->withCli(false);
        $this->assertFalse($bootstrap->isCli());
    }

    public function testWithDev(): void
    {
        $bootstrap = $this->getBootstrap()->withDev(true);
        $this->assertTrue($bootstrap->isDev());
        $bootstrap = $bootstrap->withDev(false);
        $this->assertFalse($bootstrap->isDev());
    }

    // TODO: must mock Console.
    // public function testWithConsole(): void
    // {
    //     $bootstrap = $this->getBootstrap()->withConsole(new Console);
    //     $this->assertTrue($bootstrap->hasConsole());
    //     $this->assertInstanceOf(ConsoleInterface::class, $bootstrap->console());
    // }
}
