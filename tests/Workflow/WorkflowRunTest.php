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
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

final class WorkflowRunTest extends TestCase
{
    public function testConstruct(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step',
                (new Task(WorkflowRunTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            );
        $arguments = ['foo' => 'bar'];
        $workflowRun = new WorkflowRun($workflow, $arguments);
        $this->assertMatchesRegularExpression(
            '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
            $workflowRun->uuid()
        );
        $this->assertSame($workflow, $workflowRun->workflow());
        $this->assertSame($arguments, $workflowRun->arguments()->toArray());
        $this->expectException(OutOfBoundsException::class);
        $workflowRun->get('not-found');
    }

    public function testWithAdded(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-0',
                (new Task(WorkflowRunTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            )
            ->withAdded(
                'step-1',
                (new Task(WorkflowRunTestStep2::class))
                    ->withArguments([
                        'foo' => '${step-0:response-0}',
                        'bar' => '${bar}'
                    ])
            );
        $arguments = [
            'foo' => 'hola',
            'bar' => 'mundo'
        ];
        $responseData = ['response-0' => 'value'];
        $workflowRun = (new WorkflowRun($workflow, $arguments))
            ->withAdded('step-0', new ResponseSuccess($responseData));
        $this->assertTrue($workflow->hasVar('${step-0:response-0}'));
        $this->assertTrue($workflowRun->has('step-0'));
        $this->assertSame($responseData, $workflowRun->get('step-0')->data());
    }

    public function testWithAddedNotFound(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-0',
                (new Task(WorkflowRunTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            );
        $arguments = ['foo' => 'hola'];
        $this->expectException(OutOfBoundsException::class);
        (new WorkflowRun($workflow, $arguments))
            ->withAdded('not-found', new ResponseSuccess([]));
    }

    public function testWithAddedMissingArguments(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-0',
                new Task(WorkflowRunTestStep0::class)
            )
            ->withAdded(
                'step-1',
                (new Task(WorkflowRunTestStep1::class))
                    ->withArguments(['foo' => '${step-0:response-0}'])
            );
        $this->expectException(ArgumentCountException::class);
        (new WorkflowRun($workflow, []))
            ->withAdded('step-0', new ResponseSuccess([]));
    }
}

class WorkflowRunTestStep0 extends Action
{
    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}

class WorkflowRunTestStep1 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new Parameter('foo'));
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}

class WorkflowRunTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new Parameter('foo'))
            ->withAddedRequired(new Parameter('bar'));
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
