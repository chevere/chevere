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

use Chevere\Controller\ControllerName;
use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Router\Routed;
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
