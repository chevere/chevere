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

namespace Chevere\Tests\Action;

use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Tests\Action\_resources\src\ControllerTestContextController;
use Chevere\Tests\Action\_resources\src\ControllerTestController;
use Chevere\Tests\Action\_resources\src\ControllerTestInvalidController;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testConstructInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerTestInvalidController;
    }

    public function testController(): void
    {
        $controller = new ControllerTestController;
        $this->assertFalse($controller->hasContextArguments());
        $this->assertSame(Type::STRING, $controller::PARAMETER_TYPE);
        $newController = $controller->withContextArguments([]);
        $this->assertNotEquals($controller, $newController);
        $this->assertTrue($newController->hasContextArguments());
        $this->assertEquals(new Parameters, $newController->contextParameters());
    }

    public function testContextController(): void
    {
        $contextArgument = 'contextId';
        $contextValue = 123;
        $runArgument = 'userId';
        $runValue = '321';
        $controller = (new ControllerTestContextController)
            ->withContextArguments([$contextArgument => $contextValue]);
        $this->assertTrue($controller->contextArguments()->has($contextArgument));
        $this->assertSame($contextValue, $controller->contextArguments()->get($contextArgument));
        $response = $controller->run([$runArgument => $runValue]);
        $this->assertSame([
            $runArgument => (int) $runValue,
            $contextArgument => $contextValue,
        ], $response->data());
    }
}
