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
use Chevere\Components\Workflow\Step;
use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use PHPUnit\Framework\TestCase;

final class WorkflowTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = 'test-workflow';
        $workflow = new Workflow($name);
        $this->assertSame($name, $workflow->name());
        $this->assertCount(0, $workflow);
        $this->expectException(OutOfBoundsException::class);
        $workflow->getVar('not-found');
    }

    public function testWithAdded(): void
    {
        $workflow = new Workflow('test-workflow');
        $task = new Task(WorkflowTestStep0::class);
        $step = new Step('task');
        $workflow = $workflow->withAdded($step, $task);
        $this->assertCount(1, $workflow);
        $this->assertTrue($workflow->has($step));
        $this->assertSame([$step->toString()], $workflow->order());
        $this->expectException(OverflowException::class);
        $workflow->withAdded($step, $task);
    }

    public function testWithAddedBeforeAndAfter(): void
    {
        $task = new Task(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(new Step('step'), $task)
            ->withAddedBefore(
                new Step('step'),
                new Step('step-before'),
                $task
            );
        $this->assertSame([
            'step-before',
            'step'
        ], $workflow->order());
        $workflow = $workflow
            ->withAddedAfter(
                new Step('step-before'),
                new Step('step-after'),
                $task
            );
        $this->assertSame([
            'step-before',
            'step-after',
            'step'
        ], $workflow->order());
        $this->expectException(ArgumentRequiredException::class);
        $workflow->withAdded(
            new Step('step-3'),
            (new Task(WorkflowTestStep1::class))
                ->withArguments(['missing' => '${not-found:reference}'])
        );
    }

    public function testWithAddedBeforeOutOfBounds(): void
    {
        $task = new Task(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(new Step('found'), $task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedBefore(
            new Step('not-found'),
            new Step('test'),
            $task
        );
    }

    public function testWithAddedAfterOutOfBounds(): void
    {
        $task = new Task(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(new Step('found'), $task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedAfter(
            new Step('not-found'),
            new Step('test'),
            $task
        );
    }

    public function testWithAddedTaskWithArguments(): void
    {
        $task = (new Task(WorkflowTestStep1::class))
            ->withArguments(['foo' => 'foo']);
        $step = new Step('name');
        $workflow = (new Workflow('test-workflow'))->withAdded($step, $task);
        $this->assertSame($task, $workflow->get($step));
    }

    public function testWithAddedTaskWithReferenceArguments(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                new Step('step-1'),
                (new Task(WorkflowTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $workflow = $workflow
            ->withAdded(
                new Step('step-2'),
                (new Task(WorkflowTestStep2::class))
                    ->withArguments([
                        'foo' => '${step-1:foo}',
                        'bar' => '${foo}'
                    ])
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->hasVar('${step-1:foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $this->assertSame(['step-1', 'foo'], $workflow->getVar('${step-1:foo}'));
        $task = (new Task(WorkflowTestStep1::class))
            ->withArguments(['foo' => '${not:found}']);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAdded(new Step('missing-reference'), $task);
    }
}

class WorkflowTestStep0 extends Action
{
    public function run(array $arguments): ResponseSuccessInterface
    {
        return $this->getResponseSuccess([]);
    }
}

class WorkflowTestStep1 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('foo'));
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        return $this->getResponseSuccess([]);
    }
}

class WorkflowTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(
                new StringParameter('foo'),
                new StringParameter('bar')
            );
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        return $this->getResponseSuccess([]);
    }
}
