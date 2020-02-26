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

namespace Chevere\Components\Http\Tests;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

// final class MethodControllerNameTest extends TestCase
// {
//     public function testConstruct(): void
//     {
//         $method = new GetMethod();
//         $controllerName = new ControllerName(TestController::class);
//         $methodControllerName = new MethodControllerName($method, $controllerName);
//         $this->assertSame($method, $methodControllerName->method());
//         $this->assertSame($controllerName, $methodControllerName->controllerName());
//     }
// }
