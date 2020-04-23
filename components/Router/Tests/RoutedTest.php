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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Route\RouteName;
use Chevere\Components\Router\Routed;
use PHPUnit\Framework\TestCase;

final class RoutedTest extends TestCase
{
    public function testConstruct(): void
    {
        $routeName = new RouteName('test');
        $arguments = [
            'name' => 'name-value',
            'id' => 'id-value',
        ];
        $routed = new Routed($routeName, $arguments);
        $this->assertSame($routeName, $routed->name());
        $this->assertSame($arguments, $routed->arguments());
    }
}
