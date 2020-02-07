<?php

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Routed;
use PHPUnit\Framework\TestCase;

final class RoutedTest extends TestCase
{
    public function testConstruct(): void
    {
        $route = new Route(new PathUri('/path'));
        $wildcards = [];
        $routed = new Routed($route, $wildcards);
        $this->assertSame($route, $routed->route());
        $this->assertSame($wildcards, $routed->wildcards());
    }
}
