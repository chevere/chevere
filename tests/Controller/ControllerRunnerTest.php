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

namespace Chevere\Tests\Controller;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerExecutedInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Exception;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    private function getFailedRan(ControllerInterface $controller): ControllerExecutedInterface
    {
        $arguments = new ControllerArguments($controller->parameters(), []);

        return (new ControllerRunner($controller))->execute($arguments);
    }

    public function testControllerSetUpFailure(): void
    {
        $controller = new ControllerRunnerTestControllerSetupFail;
        $ran = $this->getFailedRan($controller);
        $this->assertSame(1, $ran->code());
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
        $arguments = [$parameter => $value];
        $arguments = new ControllerArguments($controller->parameters(), $arguments);
        $ran = (new ControllerRunner($controller))->execute($arguments);
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

    public function run(ControllerArgumentsInterface $args): ControllerResponseInterface
    {
        return (new ControllerResponse(true))
            ->withData(['user' => $args->get('name')]);
    }
}

final class ControllerRunnerTestControllerSetupFail extends Controller
{
    public function setUp(): void
    {
        throw new Exception('Something went wrong');
    }

    public function run(ControllerArgumentsInterface $args): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}

final class ControllerRunnerTestControllerRunFail extends Controller
{
    public function run(ControllerArgumentsInterface $args): ControllerResponseInterface
    {
        throw new Exception('Something went wrong');
    }
}

final class ControllerRunnerTestControllerTearDownFail extends Controller
{
    public function tearDown(): void
    {
        throw new Exception('Something went wrong');
    }

    public function run(ControllerArgumentsInterface $args): ControllerResponseInterface
    {
        return new ControllerResponse(true);
    }
}
