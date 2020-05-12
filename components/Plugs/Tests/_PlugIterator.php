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

namespace Chevere\Components\Plugs\Tests;

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Plugs\PlugsIterator;
use Chevere\Components\Plugs\Types\HookPlugType;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PlugsIteratorTest extends TestCase
{
    public function testConstructInvalidDir(): void
    {
        $dir = new DirFromString(__DIR__ . '/' . uniqid() . '/');
        $this->expectException(LogicException::class);
        new PlugsIterator($dir, new HookPlugType);
    }

    public function testConstruct(): void
    {
        $dir = (new DirFromString(__DIR__ . '/'))->getChild('_resources/PlugsIteratorTest/hooks/');
        $iterator = new PlugsIterator($dir, new HookPlugType);
        $this->assertTrue(
            $iterator->plugsMap()->map()
                ->hasKey('Chevere\Components\Plugs\Tests\_resources\PlugsIteratorTest\hookables\TestHookable')
        );
    }
}

// private DirInterface $tempDir;

// public function setUp(): void
// {
//     $_resources = (new DirFromString(__DIR__ . '/'))->getChild('_resources/');
//     $this->tempDir = $_resources->getChild('temp/');
//     if ($this->tempDir->exists()) {
//         $this->tempDir->removeContents();
//     } else {
//         $this->tempDir->create();
//     }
// }

// public function tearDown(): void
// {
//     $this->tempDir->removeContents();
// }
