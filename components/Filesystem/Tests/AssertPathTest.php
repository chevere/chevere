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

namespace Chevere\Components\Filesystem\Path\Tests;

use Chevere\Components\Filesystem\AssertPath;
use Chevere\Components\Filesystem\Exceptions\Path\PathDotSlashException;
use Chevere\Components\Filesystem\Exceptions\Path\PathDoubleDotsDashException;
use Chevere\Components\Filesystem\Exceptions\Path\PathExtraSlashesException;
use Chevere\Components\Filesystem\Exceptions\Path\PathInvalidException;
use Chevere\Components\Filesystem\Exceptions\Path\PathNotAbsoluteException;
use PHPUnit\Framework\TestCase;

final class AssertPathTest extends TestCase
{
    public function testNoAbsolutePath(): void
    {
        $this->expectException(PathNotAbsoluteException::class);
        (new AssertPath('path'));
    }

    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new AssertPath('/some//dir');
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new AssertPath('/some/./dir');
    }

    public function testDotsSlashPath(): void
    {
        $this->expectException(PathDoubleDotsDashException::class);
        new AssertPath('/some/../dir');
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new AssertPath('/path'));
    }
}
