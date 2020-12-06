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
        $step = new Step(WorkflowTestStep0::class);
        $workflow = $workflow->withAdded(step: $step);
        $this->assertCount(1, $workflow);
        $this->assertTrue($workflow->has('step'));
        $this->assertSame(['step'], $workflow->order());
        $this->expectException(OverflowException::class);
        $workflow->withAdded(step: $step);
    }

    public function testWithAddedBeforeAndAfter(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(step: new Step(WorkflowTestStep0::class))
            ->withAddedBefore(
                'step',
                stepBefore: new Step(WorkflowTestStep0::class)
            );
        $this->assertSame(['stepBefore', 'step'], $workflow->order());
        $workflow = $workflow
            ->withAddedAfter(
                'stepBefore',
                stepAfter: new Step(WorkflowTestStep0::class)
            );
        $this->assertSame([
            'stepBefore',
            'stepAfter',
            'step'
        ], $workflow->order());
        $this->expectException(ArgumentRequiredException::class);
        $workflow->withAdded(
            step3: (new Step(WorkflowTestStep1::class))
                ->withArguments(missing: '${not-found:reference}')
        );
    }

    public function testWithAddedBeforeOutOfBounds(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                found: new Step(WorkflowTestStep0::class)
            );
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedBefore(
            'not-found',
            test: new Step(WorkflowTestStep0::class)
        );
    }

    public function testWithAddedAfterOutOfBounds(): void
    {
        $step = new Step(WorkflowTestStep0::class);
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(found: $step);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedAfter(
            'not-found',
            test: new Step(WorkflowTestStep0::class)
        );
    }

    public function testWithAddedTaskWithArguments(): void
    {
        $step = (new Step(WorkflowTestStep1::class))
            ->withArguments(foo: 'foo');
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(name: $step);
        $this->assertSame($step, $workflow->get('name'));
    }

    public function testWithAddedTaskWithReferenceArguments(): void
    {
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                step1: (new Step(WorkflowTestStep1::class))
                    ->withArguments(foo:  '${foo}')
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $workflow = $workflow
            ->withAdded(
                step2: (new Step(WorkflowTestStep2::class))
                    ->withArguments(
                        foo: '${step1:foo}',
                        bar: '${foo}'
                    )
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->hasVar('${step1:foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $this->assertSame(['step1', 'foo'], $workflow->getVar('${step1:foo}'));
        $step = (new Step(WorkflowTestStep1::class))
            ->withArguments(foo: '${not:found}');
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAdded($step);
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
