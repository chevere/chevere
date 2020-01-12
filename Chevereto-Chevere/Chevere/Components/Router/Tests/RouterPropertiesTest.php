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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Router\RouterProperties;
use PHPUnit\Framework\TestCase;

final class RouterPropertiesTest extends TestCase
{
    public function testConstruct(): void
    {
        $properties = new RouterProperties();
        $this->assertFalse($properties->hasRegex());
        $this->assertSame([], $properties->routes());
        $this->assertSame([], $properties->index());
        $this->assertSame([], $properties->groups());
        $this->assertSame([], $properties->named());
        $this->assertSame([
            'regex' => '',
            'routes' => [],
            'index' => [],
            'groups' => [],
            'named' => [],
        ], $properties->toArray());
    }

    public function testWithRegex(): void
    {
        $regex = '/[a-z]+/';
        $properties = (new RouterProperties())
            ->withRegex($regex);
        $this->assertTrue($properties->hasRegex());
        $this->assertSame($regex, $properties->regex());
    }

    public function testWithOthers(): void
    {
        foreach ([
            'routes' => 'withRoutes',
            'index' => 'withIndex',
            'groups' => 'withGroups',
            'named' => 'withNamed',
        ] as $property => $method) {
            $array = [$property];
            $properties = (new RouterProperties())
                ->$method($array);
            $this->assertSame($array, $properties->$property());
        }
    }
}
