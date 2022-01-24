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

namespace Chevere\Tests\Pluggable;

use function Chevere\Filesystem\dirForPath;
use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Pluggable\PlugsMapper;
use Chevere\Pluggable\Types\HookPlugType;
use PHPUnit\Framework\TestCase;

final class PlugsMapperTest extends TestCase
{
    public function testConstructInvalidDir(): void
    {
        $dir = dirForPath(__DIR__ . '/' . uniqid() . '/');
        $this->expectException(DirNotExistsException::class);
        (new PlugsMapper(new HookPlugType()))->withPlugsMapFor($dir);
    }

    public function testConstruct(): void
    {
        $dir = dirForPath(__DIR__ . '/_resources/PlugsMapperTest/');
        $plugsMapper = (new PlugsMapper(new HookPlugType()))->withPlugsMapFor($dir);
        $this->assertTrue(
            $plugsMapper->plugsMap()->hasPlugsFor(
                'Chevere\Tests\Pluggable\_resources\PlugsMapperTest\TestMappedHookable'
            )
        );
    }
}
