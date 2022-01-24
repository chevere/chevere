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

use Chevere\Controller\Controller;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use function Chevere\Parameter\stringParameter;
use Chevere\Response\Interfaces\ResponseInterface;

class SpecMakerTestPutController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return new Parameters(
            id: stringParameter(
                description: 'The user integer id',
                regex: '/^[0-9]+$/',
            ),
            name: stringParameter(
                description: 'The user name',
                regex: '/^[\w]+$/',
            )
        );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}
