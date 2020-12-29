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
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

final class ControllerTestContextController extends Controller
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters())
            ->withAddedRequired(userId: new StringParameter());
    }

    public function getContextParameters(): ParametersInterface
    {
        return (new Parameters())
            ->withAddedRequired(contextId: new IntegerParameter());
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return (new Parameters())
            ->withAddedRequired(
                userId: new IntegerParameter(),
                contextId: new IntegerParameter()
            );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponseSuccess(
            userId: (int) $arguments->getString('userId'),
            contextId: $this->contextArguments()->getInteger('contextId'),
        );
    }
}
