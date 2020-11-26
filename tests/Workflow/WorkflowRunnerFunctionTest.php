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

namespace Chevere\Tests\Workflow;

use Chevere\Components\Action\Action;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Workflow\workflowRunner;

final class WorkflowRunnerFunctionTest extends TestCase
{
    public function testWorkflowRunner(): void
    {
        $foo = 'hola';
        $bar = 'mundo';
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-1',
                (new Task(WorkflowRunnerFunctionTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            )
            ->withAdded(
                'step-2',
                (new Task(WorkflowRunnerFunctionTestStep2::class))
                    ->withArguments([
                        'foo' => '${step-1:response-1}',
                        'bar' => '${bar}'
                    ])
            );
        $arguments = ['foo' => $foo, 'bar' => $bar];
        $workflowRun = new WorkflowRun($workflow, $arguments);
        $workflowRun = workflowRunner($workflowRun);
        $action1 = new WorkflowRunnerFunctionTestStep1;
        $this->assertEquals(
            $action1
                ->run(['foo' => $foo])
                ->data(),
            $workflowRun->get('step-1')->data()
        );
        $foo = $workflowRun->get('step-1')->data()['response-1'];
        $action2 = new WorkflowRunnerFunctionTestStep2;
        $this->assertEquals(
            $action2
                ->run(
                    [
                        'foo' => $foo,
                        'bar' => $bar
                    ]
                )
                ->data(),
            $workflowRun->get('step-2')->data()
        );
    }
}

class WorkflowRunnerFunctionTestStep1 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('foo'));
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('response-1'));
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        $arguments = $this->getArguments($arguments);

        return $this->getResponseSuccess(
            [
                'response-1' => $arguments->getString('foo'),
            ]
        );
    }
}

class WorkflowRunnerFunctionTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('foo'))
            ->withAddedRequired(new StringParameter('bar'));
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('response-2'));
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        $arguments = $this->getArguments($arguments);

        return $this->getResponseSuccess(
            [
                'response-2' => $arguments->getString('foo') . ' ^ ' . $arguments->getString('bar'),
            ]
        );
    }
}
