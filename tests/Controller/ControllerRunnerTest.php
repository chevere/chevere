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
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Controller\ControllerExecutedInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Exception;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    private function getFailedRan(ControllerInterface $controller): ControllerExecutedInterface
    {
        $arguments = new Arguments($controller->parameters(), []);

        return (new ControllerRunner($controller))->execute($arguments);
    }

    public function testControllerRunFailure(): void
    {
        $controller = new ControllerRunnerTestControllerRunFail;
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
        $arguments = new Arguments($controller->parameters(), $arguments);
        $execute = (new ControllerRunner($controller))->execute($arguments);
        $this->assertSame(0, $execute->code());
        $this->assertSame(['user' => $value], $execute->data());
        $this->expectException(LogicException::class);
        (new ControllerRunner($controller))
            ->execute(new Arguments(new Parameters, []));
    }
}

final class ControllerRunnerTestController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                (new Parameter('name'))
                    ->withRegex(new Regex('/^\w+$/'))
            );
    }

    public function run(ArgumentsInterface $args): ResponseInterface
    {
        return new ResponseSuccess([
            'user' => $args->get('name')
        ]);
    }
}

final class ControllerRunnerTestControllerRunFail extends Controller
{
    public function run(ArgumentsInterface $args): ResponseInterface
    {
        throw new Exception('Something went wrong');
    }
}
