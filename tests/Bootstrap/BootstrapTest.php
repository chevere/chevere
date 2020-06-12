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
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Exceptions\Bootstrap\BootstrapDirException;
use Chevere\Interfaces\Bootstrap\BootstrapInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    private function getBootDir(string $child): DirInterface
    {
        return new DirFromString(__DIR__ . '/_resources/root/' . $child);
    }

    public function testConstruct(): void
    {
        $dir = $this->getBootDir('');
        $bootstrap = new Bootstrap($dir);
        $this->assertSame($dir, $bootstrap->dir());
        $this->assertIsInt($bootstrap->time());
        $this->assertIsInt($bootstrap->hrtime());
    }

    public function testWithNonExistentDirs(): void
    {
        $dir = $this->getBootDir(uniqid() . '/');
        $this->expectException(BootstrapDirException::class);
        new Bootstrap($dir);
    }
}
