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
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\MethodControllerNameCollection;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;
use Chevere\Components\Http\Methods\ConnectMethod;
use Chevere\Components\Http\Methods\DeleteMethod;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\HeadMethod;
use Chevere\Components\Http\Methods\OptionsMethod;
use Chevere\Components\Http\Methods\PatchMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Http\Methods\TraceMethod;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

// final class MethodControllerNameCollectionTest extends TestCase
// {
//     private function getMethodControllerName(MethodInterface $method): MethodControllerNameInterface
//     {
//         return new MethodControllerName(
//             $method,
//             new ControllerName(TestController::class)
//         );
//     }

//     public function testConstructEmpty(): void
//     {
//         $method = new GetMethod();
//         $collection = new MethodControllerNameCollection();
//         $this->assertFalse($collection->hasAny());
//         $this->assertFalse(
//             $collection->has($method)
//         );
//         $this->assertSame([], $collection->toArray());
//         $this->expectException(MethodNotFoundException::class);
//         $collection->get($method);
//     }

//     public function testwithAddedMethodControllerName(): void
//     {
//         $collection = new MethodControllerNameCollection();
//         $aux = [];
//         foreach ([
//             new ConnectMethod,
//             new DeleteMethod,
//             new GetMethod,
//             new HeadMethod,
//             new OptionsMethod,
//             new PatchMethod,
//             new PostMethod,
//             new PutMethod,
//             new TraceMethod
//         ] as $method) {
//             $methodControllerName = $this->getMethodControllerName($method);
//             $collection = $collection
//                 ->withAddedMethodControllerName($methodControllerName);
//             $this->assertTrue($collection->has($method));
//             $this->assertSame($methodControllerName, $collection->get($method));
//             $aux[] = $methodControllerName;
//         }
//         $this->assertTrue($collection->hasAny());
//         $this->assertSame($aux, $collection->toArray());
//     }
// }
