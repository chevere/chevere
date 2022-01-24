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

namespace Chevere\Tests\Router\Route\_resources\src;

use Chevere\Controller\Controller;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Regex\Regex;
use Chevere\Response\Interfaces\ResponseInterface;

final class RouteEndpointTestController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return new Parameters(
            name: (new StringParameter())
                ->withRegex(new Regex('/^[\w]+$/'))
                ->withAddedAttribute('attr'),
            id: (new StringParameter())
                ->withRegex(new Regex('/^[0-9]+$/')),
        );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}
