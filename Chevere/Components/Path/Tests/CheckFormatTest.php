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

namespace Chevere\Components\Path\Tests;

use Chevere\Components\Path\CheckFormat;
use Chevere\Components\Path\Exceptions\PathDotSlashException;
use Chevere\Components\Path\Exceptions\PathDoubleDotsDashException;
use Chevere\Components\Path\Exceptions\PathExtraSlashesException;
use Chevere\Components\Path\Exceptions\PathInvalidException;
use Chevere\Components\Path\Exceptions\PathNotAbsoluteException;
use PHPUnit\Framework\TestCase;

final class CheckFormatTest extends TestCase
{
    public function testNoAbsolutePath(): void
    {
        $this->expectException(PathNotAbsoluteException::class);
        (new CheckFormat('path'));
    }

    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new CheckFormat('/some//dir');
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new CheckFormat('/some/./dir');
    }

    public function testDotsSlashPath(): void
    {
        $this->expectException(PathDoubleDotsDashException::class);
        new CheckFormat('/some/../dir');
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new CheckFormat('/path'));
    }
}
