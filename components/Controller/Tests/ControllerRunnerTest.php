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

namespace Chevere\Components\Controller\Tests;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Interfaces\ControllerRanInterface;
use Chevere\Components\Controller\Interfaces\ControllerResponseInterface;
use Chevere\Components\Regex\Regex;
use Ds\Map;
use Exception;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    private function getFailedRan(ControllerInterface $controller): ControllerRanInterface
    {
        $arguments = new ControllerArguments($controller->parameters(), new Map);

        return (new ControllerRunner($controller))->run($arguments);
    }

    private function getControllerMethod(ControllerInterface $controller, string $method): string
    {
        return get_class($controller) . '::' . $method;
    }

    public function testControllerSetUpFailure(): void
    {
        $controller = new ControllerRunnerTestControllerSetupFail;
        $ran = $this->getFailedRan($controller);
        $this->assertSame(1, $ran->code());
        // $this->assertStringContainsString($this->getControllerMethod($controller, 'setUp'), $ran->data()[0]);
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame('Something went wrong', $ran->throwable()->getMessage());
    }

    public function testControllerRunFailure(): void
    {
        $controller = new ControllerRunnerTestControllerRunFail;
        $ran = $this->getFailedRan($controller);
        $this->assertSame(1, $ran->code());
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame('Something went wrong', $ran->throwable()->getMessage());
    }

    public function testControllerTearDownFailure(): void
    {
        $controller = new ControllerRunnerTestControllerTearDownFail;
        $ran = $this->getFailedRan($controller);
        $this->assertSame(1, $ran->code());
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame('Something went wrong', $ran->throwable()->getMessage());
    }

    public function testRunWithArguments(): void
    {
        $parameter = 'name';
        $value = 'PeterPoison';
        $controller = new ControllerRunnerTestController;
        $arguments = new Map([$parameter => $value]);
        $arguments = new ControllerArguments($controller->parameters(), $arguments);
        $ran = (new ControllerRunner($controller))->run($arguments);
        $this->assertSame(0, $ran->code());
        $this->assertSame(['user' => $value], $ran->data());
    }
}

final class ControllerRunnerTestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('name', new Regex('/^\w+$/'))
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return (new ControllerResponse(true))
            ->withData(['user' => $arguments->get('name')]);
    }
}

final class ControllerRunnerTestControllerSetupFail extends Controller
{
    public function setUp(): void
    {
        throw new Exception('Something went wrong');
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}

final class ControllerRunnerTestControllerRunFail extends Controller
{
    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        throw new Exception('Something went wrong');

        return new ControllerResponse(true);
    }
}

final class ControllerRunnerTestControllerTearDownFail extends Controller
{
    public function tearDown(): void
    {
        throw new Exception('Something went wrong');
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}
