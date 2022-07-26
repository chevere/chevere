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

use function Chevere\Filesystem\directoryForPath;
use function Chevere\Filesystem\tailDirectoryPath;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testTailDirectory(): void
    {
        $paths = [
            'string' => 'string/',
            'string/' => 'string/',
            'string\\' => 'string/',
            '/\//string\\' => '/\//string/',
        ];
        foreach ($paths as $value => $expected) {
            $path = tailDirectoryPath($value);
            $this->assertSame($expected, $path);
        }
    }

    public function testDirectoryForPath(): void
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
            'c:\string' => 'C:/string/',
            'c:\string/' => 'C:/string/',
            'c:\string\\' => 'C:/string/',
        ];
        foreach (($unix + $windows) as $value => $expected) {
            $directory = directoryForPath($value);
            $this->assertSame($expected, $directory->path()->__toString());
        }
    }
}
