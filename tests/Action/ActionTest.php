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

use Chevere\Tests\Action\_resources\ActionTestAction;
use Chevere\Tests\Action\_resources\ActionTestController;
use Chevere\Tests\Action\_resources\ActionTestGenericResponse;
use Chevere\Tests\Action\_resources\ActionTestGenericResponseError;
use Chevere\Tests\Action\_resources\ActionTestInvalidRunReturn;
use Chevere\Tests\Action\_resources\ActionTestInvalidScope;
use Chevere\Tests\Action\_resources\ActionTestMissingRun;
use Chevere\Tests\Action\_resources\ActionTestNoReturnType;
use Chevere\Tests\Action\_resources\ActionTestNoStrict;
use Chevere\Tests\Action\_resources\ActionTestRunParameterMissingType;
use Chevere\Tests\Action\_resources\ActionTestRunReturnExtraArguments;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\ErrorException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class ActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new ActionTestAction();
        $this->expectNotToPerformAssertions();
        $action->getResponse();
    }

    public function testMissingRunMethod(): void
    {
        $this->expectException(LogicException::class);
        (new ActionTestMissingRun())->assert();
    }

    public function testRunWithArguments(): void
    {
        $parameter = 'name';
        $value = 'PeoplesHernandez';
        $action = new ActionTestController();
        $arguments = [
            $parameter => $value,
        ];
        $array = $action->getResponse(...$arguments)->data();
        $expected = [
            'user' => $value,
        ];
        $this->assertSame($expected, $array);
        $response = $action->getResponse(...$arguments);
        $this->assertSame(0, $response->code());
        $this->assertSame($expected, $response->data());
    }

    public function testInvalidRunReturn(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(ActionTestInvalidRunReturn::class . '::run');
        ( new ActionTestInvalidRunReturn())->assert();
    }

    public function testInvalidRunParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('$mixed');
        (new ActionTestRunParameterMissingType())->assert();
    }

    public function testRunReturnExtraArguments(): void
    {
        $action = new ActionTestRunReturnExtraArguments();
        $this->expectException(ArgumentCountError::class);
        $action->getResponse();
    }

    public function testActionNoStrict(): void
    {
        $action = new ActionTestNoStrict();
        $this->expectNotToPerformAssertions();
        $action->getResponse();
    }

    public function testActionGenericResponse(): void
    {
        $action = new ActionTestGenericResponse();
        $this->expectNotToPerformAssertions();
        $action->getResponse();
    }

    public function testActionGenericResponseError(): void
    {
        $action = new ActionTestGenericResponseError();
        $this->expectException(InvalidArgumentException::class);
        $action->getResponse();
    }

    public function testActionInvalidScope(): void
    {
        $this->expectException(LogicException::class);
        (new ActionTestInvalidScope())->assert();
    }

    public function testActionNoReturnType(): void
    {
        $this->expectException(ErrorException::class);
        (new ActionTestNoReturnType())->assert();
    }

    public function testParametersNullAssign(): void
    {
        $action = new ActionTestAction();
        $reflection = new ReflectionProperty($action, 'parameters');
        $this->assertTrue($reflection->isInitialized($action));
        $this->assertNull($reflection->getValue($action));
        $action->getResponse();
        $object = $reflection->getValue($action);
        // vdd($object, $reflection->getValue($action));
    }
}
