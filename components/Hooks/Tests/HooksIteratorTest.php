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

namespace Chevere\Components\Hooks\Tests;

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Hooks\HooksIterator;
use LogicException;
use PHPUnit\Framework\TestCase;

final class HooksIteratorTest extends TestCase
{
    public function testConstructInvalidDir(): void
    {
        $dir = new DirFromString(__DIR__ . '/' . uniqid() . '/');
        $this->expectException(LogicException::class);
        new HooksIterator($dir);
    }

    public function testConstruct(): void
    {
        $dir = (new DirFromString(__DIR__ . '/'))->getChild('_resources/HooksIteratorTest/hooks/');
        $iterator = new HooksIterator($dir);
        $this->assertTrue(
            $iterator->hooksRegister()->hooksQueueMap()
                ->hasKey('Chevere\Components\Hooks\Tests\_resources\HooksIteratorTest\hookables\TestHookable')
        );
    }
}
