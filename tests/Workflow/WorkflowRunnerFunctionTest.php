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

use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Action\Action;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Action\ActionInterface;
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
                    ->withArguments([
                        'foo' => '${foo}'
                    ])
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
        $workflowRun = (new WorkflowRun($workflow, $arguments));
        $workflowRun = workflowRunner($workflowRun);
        $action1 = new WorkflowRunnerFunctionTestStep1;
        $this->assertEquals(
            $action1->run(
                new Arguments($action1->getParameters(), ['foo' => $foo])
            ),
            $workflowRun->get('step-1')
        );
        $foo = $workflowRun->get('step-1')->data()['response-1'];
        $action2 = new WorkflowRunnerFunctionTestStep2;
        $this->assertEquals(
            $action2->run(
                new Arguments($action2->getParameters(), [
                    'foo' => $foo,
                    'bar' => $bar
                ])
            ),
            $workflowRun->get('step-2')
        );
    }
}

class WorkflowRunnerFunctionTestStep1 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAdded(new Parameter('foo'));
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([
            'response-1' => $arguments->get('foo'),
        ]);
    }
}

class WorkflowRunnerFunctionTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAdded(new Parameter('foo'))
            ->withAdded(new Parameter('bar'));
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([
            'response-1' => $arguments->get('foo') . ' ^ ' . $arguments->get('bar'),
        ]);
    }
}
