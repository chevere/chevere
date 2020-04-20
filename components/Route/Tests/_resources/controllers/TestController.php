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

namespace Chevere\Components\Route\Tests\_resources\controllers;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Parameter;
use Chevere\Components\Controller\Parameters;
use Chevere\Components\Regex\Regex;

final class TestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new Parameters)
            ->withParameter(new Parameter('name', new Regex('/^[\w]+$/')))
            ->withParameter(new Parameter('id', new Regex('/^[0-9]+$/')));
    }

    public function run(ControllerArgumentsInterface $arguments): void
    {
        // does nothing
    }
}
