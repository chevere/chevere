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

namespace Chevere\Tests\Controller\_resources\src;

use Chevere\Controller\Controller;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Response\Interfaces\ResponseInterface;

final class ControllerTestInvalidController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return new Parameters(integer: new IntegerParameter());
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}
