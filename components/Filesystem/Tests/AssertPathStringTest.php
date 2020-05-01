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

use Chevere\Components\Filesystem\AssertPathString;
use Chevere\Components\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Components\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Components\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Components\Filesystem\Exceptions\PathNotAbsoluteException;
use PHPUnit\Framework\TestCase;

final class AssertPathStringTest extends TestCase
{
    public function testNoAbsolutePath(): void
    {
        $this->expectException(PathNotAbsoluteException::class);
        (new AssertPathString('path'));
    }

    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new AssertPathString('/some//dir');
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new AssertPathString('/some/./dir');
    }

    public function testDotsSlashPath(): void
    {
        $this->expectException(PathDoubleDotsDashException::class);
        new AssertPathString('/some/../dir');
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new AssertPathString('/path'));
    }
}
