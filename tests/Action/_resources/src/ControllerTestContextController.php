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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Components\Action\Controller;
use Chevere\Components\Parameter\IntegerParameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;

final class ControllerTestContextController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                new StringParameter('userId')
            );
    }

    public function getContextParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                new IntegerParameter('contextId')
            );
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                new IntegerParameter('userId')
            )
            ->withAddedRequired(
                new IntegerParameter('contextId')
            );
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        $arguments = $this->getArguments($arguments);

        return $this->getResponseSuccess([
            'userId' => (int) $arguments->getString('userId'),
            'contextId' => $this->contextArguments()->getInteger('contextId')
        ]);
    }
}
