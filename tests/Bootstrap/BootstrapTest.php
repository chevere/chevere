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
use Chevere\Components\Filesystem\FilesystemFactory;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    public function testConstruct(): void
    {
        $dir = (new FilesystemFactory)
            ->getDirFromString(__DIR__ . '/_resources/root/');
        $bootstrap = new Bootstrap($dir);
        $this->assertSame($dir, $bootstrap->dir());
        $this->assertIsInt($bootstrap->time());
        $this->assertIsInt($bootstrap->hrtime());
    }
}
