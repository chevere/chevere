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
                'step-0',
                (new Task('Chevere\Tests\Workflow\workflowRunnerFunctionTestStep0'))
                    ->withArguments('${foo}')
            )
            ->withAdded(
                'step-1',
                (new Task('Chevere\Tests\Workflow\workflowRunnerFunctionTestStep1'))
                    ->withArguments('${step-0:response-0}', '${bar}')
            );
        $arguments = ['foo' => $foo, 'bar' => $bar];
        $workflowRun = (new WorkflowRun($workflow, $arguments));
        $workflowRun = workflowRunner($workflowRun);
        $this->assertEquals(workflowRunnerFunctionTestStep0($foo), $workflowRun->get('step-0'));
        $response0 = $workflowRun->get('step-0')->data()['response-0'];
        $this->assertEquals(workflowRunnerFunctionTestStep1($response0, $bar), $workflowRun->get('step-1'));
    }
}

function workflowRunnerFunctionTestStep0(string $foo): ResponseInterface
{
    return new ResponseSuccess([
        'response-0' => $foo,
    ]);
}

function workflowRunnerFunctionTestStep1(string $response0, string $bar): ResponseInterface
{
    return new ResponseSuccess([
        'response-1' => $response0 . ' ^ ' . $bar,
    ]);
}
