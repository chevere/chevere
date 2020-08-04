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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\ActionInterface;
use PHPUnit\Framework\TestCase;

final class WorkflowTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = 'test-workflow';
        $workflow = new Workflow($name);
        $this->assertSame($name, $workflow->name());
        $this->assertCount(0, $workflow);
    }

    public function testWithAdded(): void
    {
        $workflow = new Workflow('test-workflow');
        $task = new Task(WorkflowTestStep0::class);
        $step = 'task';
        $workflow = $workflow->withAdded($step, $task);
        $this->assertCount(1, $workflow);
        $this->assertTrue($workflow->has($step));
        $this->assertSame([$step], $workflow->order());
        $this->expectException(OverflowException::class);
        $workflow->withAdded($step, $task);
    }

    public function testWithAddedBeforeAndAfter(): void
    {
        $task = new Task(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('step', $task)
            ->withAddedBefore('step', 'step-before', $task);
        $this->assertSame([
            'step-before',
            'step'
        ], $workflow->order());
        $workflow = $workflow
            ->withAddedAfter('step-before', 'step-after', $task);
        $this->assertSame([
            'step-before',
            'step-after',
            'step'
        ], $workflow->order());
        $this->expectException(InvalidArgumentException::class);
        $workflow->withAdded(
            'step-3',
            (new Task(WorkflowTestStep1::class))
                ->withArguments('${not-found:reference}')
        );
    }

    public function testWithAddedBeforeOutOfBounds(): void
    {
        $task = new Task(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('found', $task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedBefore('not-fond', 'test', $task);
    }

    public function testWithAddedAfterOutOfBounds(): void
    {
        $task = new Task(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('found', $task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedAfter('not-fond', 'test', $task);
    }

    public function testWithAddedTaskWithArguments(): void
    {
        $task = (new Task(WorkflowTestStep1::class))
            ->withArguments('foo');
        $name = 'name';
        $workflow = (new Workflow('test-workflow'))->withAdded($name, $task);
        $this->assertSame($task, $workflow->get($name));
    }

    public function testWithAddedTaskWithReferenceArguments(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step-1',
                (new Task(WorkflowTestStep1::class))
                    ->withArguments('${foo}')
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $workflow = $workflow
            ->withAdded(
                'step-2',
                (new Task(WorkflowTestStep2::class))
                    ->withArguments('${step-1:foo}', '${foo}')
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->hasVar('${step-1:foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $this->assertSame(['step-1', 'foo'], $workflow->getVar('${step-1:foo}'));
        $this->expectException(InvalidArgumentException::class);
        $workflow->withAdded(
            'missing-reference',
            (new Task('callable'))->withArguments('${not:found}')
        );
    }
}

class WorkflowTestStep0 implements ActionInterface
{
    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}

class WorkflowTestStep1 implements ActionInterface
{
    public function __construct(string $foo)
    {
    }

    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}

class WorkflowTestStep2 implements ActionInterface
{
    public function __construct(string $foo, string $bar)
    {
    }

    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
