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
use Chevere\Components\Router\Properties\IndexProperty;
use PHPUnit\Framework\TestCase;

final class IndexPropertyTest extends TestCase
{
    public function testInvalidConstructor(): void
    {
        $this->expectException(RouterPropertyException::class);
        new IndexProperty([]);
    }

    public function testBadConstructor(): void
    {
        $this->expectException(RouterPropertyException::class);
        new IndexProperty(['']);
    }

    public function testConstructor(): void
    {
        $array = [
            '/' => [
                'id' => 1,
                'group' => 'group1',
                'name' => 'test.name',
            ],
            '/hello-world' => [
                'id' => 2,
                'group' => 'group2',
                'name' => null,
            ],
        ];
        $property = new IndexProperty($array);
        $this->assertSame($array, $property->toArray());
    }
}
