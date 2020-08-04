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

use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\ActionInterface;
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
                    ->withArguments('${foo}')
            )
            ->withAdded(
                'step-2',
                (new Task(WorkflowRunnerFunctionTestStep2::class))
                    ->withArguments('${step-1:response-1}', '${bar}')
            );
        $arguments = ['foo' => $foo, 'bar' => $bar];
        $workflowRun = (new WorkflowRun($workflow, $arguments));
        $workflowRun = workflowRunner($workflowRun);
        $this->assertEquals(
            (new WorkflowRunnerFunctionTestStep1($foo))->execute(),
            $workflowRun->get('step-1')
        );
        $response0 = $workflowRun->get('step-1')->data()['response-1'];
        $this->assertEquals(
            (new WorkflowRunnerFunctionTestStep2($response0, $bar))->execute(),
            $workflowRun->get('step-2')
        );
    }
}

class WorkflowRunnerFunctionTestStep1 implements ActionInterface
{
    private string $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([
            'response-1' => $this->foo,
        ]);
    }
}

class WorkflowRunnerFunctionTestStep2 implements ActionInterface
{
    private string $response0;

    public function __construct(string $response0, string $bar)
    {
        $this->response0 = $response0;
        $this->bar = $bar;
    }

    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([
            'response-1' => $this->response0 . ' ^ ' . $this->bar,
        ]);
    }
}
