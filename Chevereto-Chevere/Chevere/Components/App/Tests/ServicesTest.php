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

namespace Chevere\Components\App\Tests;

use Chevere\Components\Api\Api;
use Chevere\Components\App\Services;
use Chevere\Components\Router\Router;
use PHPUnit\Framework\TestCase;

final class ServicesTest extends TestCase
{
    public function testConstructor(): void
    {
        $this->expectNotToPerformAssertions();
        new Services();
    }

    public function testWithApi(): void
    {
        $api = new Api();
        $services = (new Services())
          ->withApi($api);
        
        $this->assertTrue($services->hasApi());
        $this->assertSame($api, $services->api());
    }

    public function testWithRouter(): void
    {
        $router = new Router();
        $services = (new Services())
          ->withRouter($router);
        
        $this->assertTrue($services->hasRouter());
        $this->assertSame($router, $services->router());
    }
}
