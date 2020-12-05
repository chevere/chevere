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
        $task = new Step('task', WorkflowTestStep0::class);
        $workflow = $workflow->withAdded($task);
        $this->assertCount(1, $workflow);
        $this->assertTrue($workflow->has('task'));
        $this->assertSame(['task'], $workflow->order());
        $this->expectException(OverflowException::class);
        $workflow->withAdded($task);
    }

    public function testWithAddedBeforeAndAfter(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(new Step('step', WorkflowTestStep0::class))
            ->withAddedBefore(
                'step',
                new Step('step-before', WorkflowTestStep0::class)
            );
        $this->assertSame(['step-before', 'step'], $workflow->order());
        $workflow = $workflow
            ->withAddedAfter(
                'step-before',
                new Step('step-after', WorkflowTestStep0::class)
            );
        $this->assertSame([
            'step-before',
            'step-after',
            'step'
        ], $workflow->order());
        $this->expectException(ArgumentRequiredException::class);
        $workflow->withAdded(
            (new Step('step-3', WorkflowTestStep1::class))
                ->withArguments(['missing' => '${not-found:reference}'])
        );
    }

    public function testWithAddedBeforeOutOfBounds(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                new Step('found', WorkflowTestStep0::class)
            );
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedBefore(
            'not-found',
            new Step('test', WorkflowTestStep0::class)
        );
    }

    public function testWithAddedAfterOutOfBounds(): void
    {
        $task = new Step('found', WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded($task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedAfter(
            'not-found',
            new Step('test', WorkflowTestStep0::class)
        );
    }

    public function testWithAddedTaskWithArguments(): void
    {
        $task = (new Step('name', WorkflowTestStep1::class))
            ->withArguments(['foo' => 'foo']);
        $workflow = (new Workflow('test-workflow'))->withAdded($task);
        $this->assertSame($task, $workflow->get('name'));
    }

    public function testWithAddedTaskWithReferenceArguments(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                (new Step('step-1', WorkflowTestStep1::class))
                    ->withArguments(['foo' => '${foo}'])
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $workflow = $workflow
            ->withAdded(
                (new Step('step-2', WorkflowTestStep2::class))
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
        $task = (new Step('missing-reference', WorkflowTestStep1::class))
            ->withArguments(['foo' => '${not:found}']);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAdded($task);
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
