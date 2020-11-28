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
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Interfaces\Controller\ControllerExecutedInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Exception;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    private function getFailedRan(ControllerInterface $controller): ControllerExecutedInterface
    {
        return (new ControllerRunner($controller))->execute([]);
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
        $execute = (new ControllerRunner($controller))->execute($arguments);
        $this->assertSame(0, $execute->code());
        $this->assertSame(['user' => $value], $execute->data());
    }
}

final class ControllerRunnerTestController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                (new StringParameter('name'))
                    ->withRegex(new Regex('/^\w+$/'))
            );
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('user'));
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        return new ResponseSuccess(new Parameters, []);
        $arguments = $this->getArguments($arguments);

        return $this->getResponseSuccess([
            'user' => 'eee'
        ]);
    }
}

final class ControllerRunnerTestControllerRunFail extends Controller
{
    public function run(array $arguments): ResponseSuccessInterface
    {
        throw new Exception('Something went wrong');
    }
}
