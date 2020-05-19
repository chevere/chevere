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

namespace Chevere\Tests\Plugin;

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Components\Plugin\PlugsMapper;
use Chevere\Components\Plugin\Types\HookPlugType;
use PHPUnit\Framework\TestCase;

final class PlugsMapperTest extends TestCase
{
    public function testConstructInvalidDir(): void
    {
        $dir = new DirFromString(__DIR__ . '/' . uniqid() . '/');
        $this->expectException(DirNotExistsException::class);
        new PlugsMapper($dir, new HookPlugType);
    }

    public function testConstruct(): void
    {
        $dir = (new DirFromString(__DIR__ . '/_resources/PlugsMapperTest/'));
        $plugsMapper = new PlugsMapper($dir, new HookPlugType);
        $this->assertTrue(
            $plugsMapper->plugsMap()->hasPluggableName(
                'Chevere\Tests\Plugin\_resources\PlugsMapperTest\TestMappedHookable'
            )
        );
    }
}
