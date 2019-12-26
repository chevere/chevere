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
use Chevere\Components\Router\Properties\NamedProperty;
use PHPUnit\Framework\TestCase;

final class NamedPropertyTest extends TestCase
{
    public function testConstructorWithEmpty(): void
    {
        $this->expectNotToPerformAssertions();
        new NamedProperty([]);
    }

    public function testBadConstructor(): void
    {
        $this->expectException(RouterPropertyException::class);
        new NamedProperty(['']);
    }

    public function testConstructor(): void
    {
        $array = [
            'test-0' => 0,
            'test-1' => 1,
            'test-2' => 2,
        ];
        $property = new NamedProperty($array);
        $this->assertSame($array, $property->toArray());
    }
}
