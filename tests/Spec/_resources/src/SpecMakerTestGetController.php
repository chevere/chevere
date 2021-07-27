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

namespace Chevere\Tests\Spec\_resources\src;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use function Chevere\Components\Parameter\stringParameter;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

class SpecMakerTestGetController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters(
            id: stringParameter(
                description: 'The user integer id',
                regex: '/^[0-9]+$/'
            )
        ))
            ->withAddedOptional(
                name: stringParameter(
                    description: 'The user name',
                    regex: '/^[\w]+$/'
                )
            );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}
