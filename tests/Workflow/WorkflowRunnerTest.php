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

use Chevere\Components\Workflow\Step;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Components\Workflow\WorkflowRunner;
use Chevere\Tests\Workflow\_resources\src\WorkflowRunnerFunctionTestStep1;
use Chevere\Tests\Workflow\_resources\src\WorkflowRunnerFunctionTestStep2;
use PHPUnit\Framework\TestCase;

final class WorkflowRunnerTest extends TestCase
{
    public function testWorkflowRunner(): void
    {
        $foo = 'hola';
        $bar = 'mundo';
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                new Step('step-1'),
                (new Task(WorkflowRunnerFunctionTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            )
            ->withAdded(
                new Step('step-2'),
                (new Task(WorkflowRunnerFunctionTestStep2::class))
                    ->withArguments([
                        'foo' => '${step-1:response-1}',
                        'bar' => '${bar}'
                    ])
            );
        $arguments = ['foo' => $foo, 'bar' => $bar];
        $workflowRun = new WorkflowRun($workflow, $arguments);
        $container = [];
        $workflowRunner = new WorkflowRunner($workflowRun);
        $workflowRun = $workflowRunner->run($container);
        $this->assertSame($workflowRun, $workflowRunner->workflowRun());
        $action1 = new WorkflowRunnerFunctionTestStep1;
        $this->assertEquals(
            $action1->run(['foo' => $foo])->data(),
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
