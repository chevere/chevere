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

use function Chevere\Components\Filesystem\tailDirPath;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testTailDir(): void
    {
        $noTrailing = 'some string';
        $dirWithTail = tailDirPath($noTrailing);
        $this->assertNotSame($noTrailing, $dirWithTail);
        $this->assertStringEndsWith('/', $dirWithTail);
        $trailing = 'some string/';
        $dirWithTail = tailDirPath($trailing);
        $this->assertSame($trailing, $dirWithTail);
    }
}
