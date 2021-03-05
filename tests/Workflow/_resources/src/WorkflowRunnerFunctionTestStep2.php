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

namespace Chevere\Tests\Workflow\_resources\src;

use Chevere\Components\Action\Action;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

class WorkflowRunnerFunctionTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return new Parameters(
            foo: new StringParameter(),
            bar: new StringParameter()
        );
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return new Parameters(response2: new StringParameter());
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse(
            response2: $arguments->getString('foo') .
                    ' ^ ' . $arguments->getString('bar')
        );
    }
}
