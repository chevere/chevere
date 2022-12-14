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

use Chevere\Container\Container;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Tests\Action\_resources\src\ActionTestAction;
use Chevere\Tests\Action\_resources\src\ActionTestContainer;
use Chevere\Tests\Action\_resources\src\ActionTestController;
use Chevere\Tests\Action\_resources\src\ActionTestInvalidRunParameter;
use Chevere\Tests\Action\_resources\src\ActionTestInvalidRunReturn;
use Chevere\Tests\Action\_resources\src\ActionTestMissingRun;
use Chevere\Tests\Action\_resources\src\ActionTestParameterAttributes;
use Chevere\Tests\Action\_resources\src\ActionTestRunParameters;
use Chevere\Tests\Action\_resources\src\ActionTestSetupBeforeAndAfter;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new ActionTestAction();
        $this->assertSame('test', $action->description());
        $this->assertCount(0, $action->parameters());
        $this->assertCount(1, $action->responseParameters());
        $action->run();
    }

    public function testActionMissingRun(): void
    {
        $this->expectException(LogicException::class);
        new ActionTestMissingRun();
    }

    public function testActionParams(): void
    {
        $defaults = [
            'intDefault' => 1,
            'stringDefault' => 'default',
            'boolDefault' => false,
            'floatDefault' => 0.0,
            'arrayDefault' => [],
            'objectDefault' => null,
        ];
        $types = [
            'int' => IntegerParameterInterface::class,
            'string' => StringParameterInterface::class,
            'bool' => BooleanParameterInterface::class,
            'float' => FloatParameterInterface::class,
            'array' => ArrayParameterInterface::class,
            'object' => ObjectParameterInterface::class,
            'file' => ObjectParameterInterface::class,
        ];
        $optional = array_keys($defaults);
        $required = array_keys($types);
        $action = new ActionTestRunParameters();
        $this->assertSame($optional, $action->parameters()->optional());
        $this->assertSame($required, $action->parameters()->required());
        foreach ($defaults as $name => $value) {
            $parameter = $action->parameters()->get(strval($name));
            $this->assertSame($value, $parameter->default());
        }
        foreach ($types as $parameter => $class) {
            $parameter = strval($parameter);
            $this->assertInstanceOf($class, $action->parameters()->get($parameter));
        }
        $this->assertSame(
            FileInterface::class,
            $action->parameters()->get('file')->type()->typeHinting()
        );
    }

    public function testActionParamsAttributes(): void
    {
        $action = new ActionTestParameterAttributes();
        $this->assertSame('An int', $action->parameters()->get('int')->description());
        /** @var StringParameterInterface $parameter */
        $parameter = $action->parameters()->get('name');
        $this->assertSame('The name', $parameter->description());
        $this->assertSame('/^[a-z]$/', $parameter->regex()->__toString());
    }

    public function testActionContainer(): void
    {
        $container = new Container();
        $containerWith = $container->withPut(id: 123, name: 'wea');
        $this->assertNotSame($container, $containerWith);
        $action = new ActionTestContainer();
        $withContainer = $action->withContainer($containerWith);
        $this->assertNotSame($action, $withContainer);
        $response = $withContainer->getResponse();
        $this->assertSame(0, $response->code());
        $this->expectException(InvalidArgumentException::class);
        $action->withContainer($container);
    }

    public function testActionContainerMissingParameterException(): void
    {
        $action = new ActionTestContainer();
        $this->expectExceptionMessage('[id, name]');
        $this->expectException(InvalidArgumentException::class);
        $action->getResponse();
    }

    public function testActionRunWithArguments(): void
    {
        $parameter = 'name';
        $value = 'PeoplesHernandez';
        $action = new ActionTestController();
        $arguments = [
            $parameter => $value,
        ];
        $array = $action->run(...$arguments);
        $expected = [
            'user' => $value,
        ];
        $this->assertSame($expected, $array);
        $response = $action->getResponse(...$arguments);
        $this->assertSame(0, $response->code());
        $this->assertSame($expected, $response->data());
    }

    public function testActionInvalidRunReturn(): void
    {
        $action = new ActionTestInvalidRunReturn();
        $data = $action->run();
        $this->assertIsNotArray($data);
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(ActionTestInvalidRunReturn::class . '::run');
        $action->getResponse();
    }

    public function testActionInvalidRunParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('$mixed');
        new ActionTestInvalidRunParameter();
    }

    public function testSetupBeforeAndAfter(): void
    {
        $action = new ActionTestSetupBeforeAndAfter();
        $this->assertSame(1, $action->before());
        $this->assertSame(2, $action->after());
    }
}
