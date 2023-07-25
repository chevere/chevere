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
use Chevere\Tests\Action\_resources\ActionTestMissingRun;
use Chevere\Tests\Action\_resources\ActionTestNullReturnType;
use Chevere\Tests\Action\_resources\ActionTestPrivateScope;
use Chevere\Tests\Action\_resources\ActionTestRunParameterMissingType;
use Chevere\Tests\Action\_resources\ActionTestRunReturnExtraArguments;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Error;
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
        (new ActionTestMissingRun())->getResponse();
    }

    public function testWithArguments(): void
    {
        $expected = 'PeoplesHernandez';
        $action = new ActionTestController();
        $string = $action->getResponse(name: $expected)->string();
        $this->assertSame($expected, $string);
    }

    public function testInvalidRunParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('$mixed');
        (new ActionTestRunParameterMissingType())->getResponse();
    }

    public function testReturnExtraArguments(): void
    {
        $action = new ActionTestRunReturnExtraArguments();
        $this->expectException(ArgumentCountError::class);
        $action->getResponse();
    }

    public function testGenericResponse(): void
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

    public function testPrivateScope(): void
    {
        $this->expectException(Error::class);
        (new ActionTestPrivateScope())->getResponse();
    }

    public function testNullReturnType(): void
    {
        $this->expectNotToPerformAssertions();
        (new ActionTestNullReturnType())->getResponse();
    }

    public function testParametersNullAssign(): void
    {
        $action = new ActionTestAction();
        $reflection = new ReflectionProperty($action, 'parameters');
        $this->assertTrue($reflection->isInitialized($action));
        $this->assertNull($reflection->getValue($action));
        $action->getResponse();
        $reflection->getValue($action);
    }
}
