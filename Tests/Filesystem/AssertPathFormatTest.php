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

namespace Chevere\Tests\Filesystem;

use Chevere\Components\Filesystem\AssertPathFormat;
use Chevere\Exceptions\Filesystem\PathDotSlashException;
use Chevere\Exceptions\Filesystem\PathDoubleDotsDashException;
use Chevere\Exceptions\Filesystem\PathExtraSlashesException;
use Chevere\Exceptions\Filesystem\PathNotAbsoluteException;
use PHPUnit\Framework\TestCase;

final class AssertPathFormatTest extends TestCase
{
    public function testNoAbsolutePath(): void
    {
        $this->expectException(PathNotAbsoluteException::class);
        (new AssertPathFormat('path'));
    }

    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new AssertPathFormat('/some//dir');
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new AssertPathFormat('/some/./dir');
    }

    public function testDotsSlashPath(): void
    {
        $this->expectException(PathDoubleDotsDashException::class);
        new AssertPathFormat('/some/../dir');
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new AssertPathFormat('/path'));
    }
}
