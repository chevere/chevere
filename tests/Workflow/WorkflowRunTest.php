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
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class WorkflowRunTest extends TestCase
{
    public function testConstruct(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step',
                (new Task('callable'))
                    ->withArguments('${foo}')
            );
        $arguments = new Arguments(
            $workflow->parameters(),
            ['foo' => 'bar']
        );
        $workflowRun = new WorkflowRun($workflow, $arguments);
        $this->assertSame($workflow, $workflowRun->workflow());
        $this->assertSame($arguments, $workflowRun->arguments());
        $this->expectException(OutOfBoundsException::class);
        $workflowRun->get('not-found');
    }

    public function testWithAdded(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-0',
                (new Task('callable'))
                    ->withArguments('${foo}')
            )
            ->withAdded(
                'step-1',
                (new Task('callable'))
                    ->withArguments('${step-0:response-0}', '${bar}')
            );
        $arguments = new Arguments(
            $workflow->parameters(),
            [
                'foo' => 'hola',
                'bar' => 'mundo'
            ]
        );
        $responseData = ['response-0' => 'value'];
        $workflowRun = (new WorkflowRun($workflow, $arguments))
            ->withAdded('step-0', new ResponseSuccess($responseData));
        $this->assertTrue($workflowRun->has('step-0'));
        $this->assertSame($responseData, $workflowRun->get('step-0'));
    }

    public function testWithAddedNotFound(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-0',
                (new Task('callable'))
                    ->withArguments('${foo}')
            );
        $arguments = new Arguments(
            $workflow->parameters(),
            ['foo' => 'hola']
        );
        $this->expectException(OutOfBoundsException::class);
        (new WorkflowRun($workflow, $arguments))
            ->withAdded('not-found', new ResponseSuccess([]));
    }

    public function testWithAddedMissingArguments(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-0',
                new Task('callable')
            )
            ->withAdded(
                'step-1',
                (new Task('callable'))
                    ->withArguments('${step-0:response-0}')
            );
        $arguments = new Arguments(
            $workflow->parameters(),
            []
        );
        $this->expectException(InvalidArgumentException::class);
        (new WorkflowRun($workflow, $arguments))
            ->withAdded('step-0', new ResponseSuccess([]));
    }
}
