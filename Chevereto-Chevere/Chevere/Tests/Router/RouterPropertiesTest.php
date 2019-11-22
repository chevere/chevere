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

namespace Chevere\Tests\Router;

use Chevere\Components\Router\RouterProperties;
use PHPUnit\Framework\TestCase;

final class RouterPropertiesTest extends TestCase
{
    public function testConstruct(): void
    {
        $properties = new RouterProperties();
        $this->assertSame('', $properties->regex());
        $this->assertSame([], $properties->routes());
        $this->assertSame([], $properties->index());
        $this->assertSame([], $properties->groups());
        $this->assertSame([], $properties->named());
    }

    // public function testRegex(): void
    // {
    // }
}
