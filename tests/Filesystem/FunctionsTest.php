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

use function Chevere\Filesystem\dirForPath;
use function Chevere\Filesystem\tailDirPath;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testTailDir(): void
    {
        $paths = [
            'string' => 'string/',
            'string/' => 'string/',
            'string\\' => 'string/',
            '/\//string\\' => '/\//string/',
        ];
        foreach ($paths as $value => $expected) {
            $path = tailDirPath($value);
            $this->assertSame($expected, $path);
        }
    }

    public function testDirForPath(): void
    {
        $unix = [
            '/string' => '/string/',
            '/string/' => '/string/',
            '/string\\' => '/string/',
        ];
        $windows = [
            'C:\string' => 'C:/string/',
            'C:\string/' => 'C:/string/',
            'C:\string\\' => 'C:/string/',
            'c:\string' => 'c:/string/',
            'c:\string/' => 'c:/string/',
            'c:\string\\' => 'c:/string/',
        ];
        foreach (($unix + $windows) as $value => $expected) {
            $dir = dirForPath($value);
            $this->assertSame($expected, $dir->path()->__toString());
        }
    }
}
