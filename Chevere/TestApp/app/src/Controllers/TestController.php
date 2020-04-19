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

namespace Chevere\TestApp\App\Controllers;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Regex\Regex;

/**
 * Note to self
 *
 * Los parametros de ruta deben ser tomados desde (este) controlador hasta la
 * la definicion de endpoint (RouteEndpoint). La ruta no deberia permitir definir
 * wildcards matches ya que se deben respetar los parametros del controlador al
 * cual esta enchufado.
 *
 */
class TestController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withPut(new ControllerParameter('name', new Regex('/^[\w]+$/')))
            ->withPut(new ControllerParameter('id', new Regex('/^[0-9]+$/')));
    }

    public function run(ControllerArgumentsInterface $arguments): void
    {
        // does nothing
    }
}
