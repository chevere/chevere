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

namespace Chevere\Tests\Router\Properties;

use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Router\Properties\GroupsProperty;
use PHPUnit\Framework\TestCase;

final class GroupsPropertyTest extends TestCase
{
    public function testInvalidConstructor(): void
    {
        $this->expectException(RouterPropertyException::class);
        new GroupsProperty([]);
    }

    public function testBadConstructor(): void
    {
        $this->expectException(RouterPropertyException::class);
        new GroupsProperty(['', 1]);
    }

    public function testConstructor(): void
    {
        $array = [
            'key1' => [
                0 => 0,
                1 => 1,
            ],
            'key2' => [
                0 => 2,
            ],
        ];
        $property = new GroupsProperty($array);
        $this->assertSame($array, $property->toArray());
    }
}
