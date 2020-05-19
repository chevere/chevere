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

namespace Chevere\Tests\Bootstrap;

use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Exceptions\Bootstrap\BootstrapDirException;
use Chevere\Interfaces\Bootstrap\BootstrapInterface;
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Interfaces\Filesystem\DirInterface;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    private function getRootDir(): DirInterface
    {
        return new DirFromString(__DIR__ . '/_resources/root/');
    }

    private function getAppDir(DirInterface $rootDir): DirInterface
    {
        return $rootDir->getChild('app/');
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
    }

    public function testWithNonExistentDirs(): void
    {
        $rootDir = $this->getRootDir();
        $appDir = $this->getAppDir($rootDir)->getChild(uniqid() . '/');
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
}
