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

namespace Chevere\Tests\Router;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Router\Routed;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Tests\Router\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RoutedTest extends TestCase
{
    public function testConstruct(): void
    {
        $controllerName = new ControllerName(TestController::class);
        $arguments = [
            'name' => 'name-value',
            'id' => 'id-value',
        ];
        $routed = new Routed($controllerName, $arguments);
        $this->assertSame($controllerName, $routed->controllerName());
        $this->assertSame($arguments, $routed->arguments());
        $this->assertInstanceOf(ControllerInterface::class, $routed->getController());
    }
}
