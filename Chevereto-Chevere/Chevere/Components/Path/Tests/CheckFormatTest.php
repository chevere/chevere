<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Path\Tests;

use Chevere\Components\Path\CheckFormat;
use Chevere\Components\Path\Exceptions\PathDoubleDotsException;
use Chevere\Components\Path\Exceptions\PathExtraSlashesException;
use Chevere\Components\Path\Exceptions\PathOmitRelativeException;
use PHPUnit\Framework\TestCase;

final class CheckFormatTest extends TestCase
{
    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new CheckFormat('some//dir');
    }

    public function testDotsPath(): void
    {
        $this->expectException(PathDoubleDotsException::class);
        new CheckFormat('some/../dir');
    }

    public function testNoRelativePath(): void
    {
        $this->expectException(PathOmitRelativeException::class);
        (new CheckFormat('./relative'))
            ->assertNotRelativePath();
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new CheckFormat('path'))
            ->assertNotRelativePath();
    }
}
