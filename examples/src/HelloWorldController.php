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

namespace Chevere\Examples;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;

class HelloWorldController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('name', new Regex('/\w+/'))
            );
    }

    public function getDescription(): string
    {
        return 'It returns Hello, <name>';
    }

    public function run(ControllerArgumentsInterface $controllerArguments): ControllerResponseInterface
    {
        $greet = sprintf('Hello, %s', $controllerArguments->get('name'));

        return (new ControllerResponse(true))
            ->withData([$greet]);
    }
}
