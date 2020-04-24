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
use Chevere\Components\Controller\ControllerArgumentsMaker;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Interfaces\ControllerResponseInterface;
use Chevere\Components\Regex\Regex;
use Ds\Map;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
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
