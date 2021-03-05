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
use Chevere\Components\Workflow\Workflow;
use Chevere\Exceptions\Core\BadMethodCallException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Tests\Workflow\_resources\src\WorkflowTestStep0;
use Chevere\Tests\Workflow\_resources\src\WorkflowTestStep1;
use Chevere\Tests\Workflow\_resources\src\WorkflowTestStep2;
use Chevere\Tests\Workflow\_resources\src\WorkflowTestStepDeps0;
use Chevere\Tests\Workflow\_resources\src\WorkflowTestStepDeps1;
use PHPUnit\Framework\TestCase;

final class WorkflowTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $workflow = new Workflow();
        $this->assertCount(0, $workflow);
        $this->expectException(OutOfBoundsException::class);
        $workflow->getVar('not-found');
    }

    public function testConstruct(): void
    {
        $step = new Step(WorkflowTestStep0::class);
        $workflow = new Workflow(
            step: $step
        );
        $this->assertCount(1, $workflow);
        $this->assertTrue($workflow->has('step'));
        $this->assertSame(['step'], $workflow->order());
    }

    public function testWithAdded(): void
    {
        $workflow = new Workflow();
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
        $workflow = (new Workflow())
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
            'step',
        ], $workflow->order());
        $this->expectException(BadMethodCallException::class);
        $workflow->withAdded(
            step3: new Step(
                WorkflowTestStep1::class,
                missing: '${not-found:reference}'
            )
        );
    }

    public function testWithAddedBeforeOutOfBounds(): void
    {
        $workflow = (new Workflow())
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
        $workflow = (new Workflow())
            ->withAdded(found: $step);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedAfter(
            'not-found',
            test: new Step(WorkflowTestStep0::class)
        );
    }

    public function testWithAddedTaskWithArguments(): void
    {
        $step = new Step(
            WorkflowTestStep1::class,
            foo: 'foo'
        );
        $workflow = (new Workflow())
            ->withAdded(name: $step);
        $this->assertSame($step, $workflow->get('name'));
    }

    public function testWithAddedTaskWithReferenceArguments(): void
    {
        $workflow = new Workflow(
            step1: new Step(
                WorkflowTestStep1::class,
                foo: '${foo}'
            )
        );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $workflow = $workflow
            ->withAdded(
                step2: new Step(
                    WorkflowTestStep2::class,
                    foo: '${step1:foo}',
                    bar: '${foo}'
                )
            );
        $this->assertTrue($workflow->hasVar('${foo}'));
        $this->assertTrue($workflow->hasVar('${step1:foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertSame(['foo'], $workflow->getVar('${foo}'));
        $this->assertSame(['step1', 'foo'], $workflow->getVar('${step1:foo}'));
        $step = new Step(
            WorkflowTestStep1::class,
            foo: '${not:found}'
        );
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAdded($step);
    }

    public function testConflictingTypeDependentActions(): void
    {
        $workflow = new Workflow(
            step1: new Step(WorkflowTestStepDeps0::class)
        );
        foreach ((new WorkflowTestStepDeps0())->dependencies()->getGenerator() as $key => $className) {
            $this->assertSame($className, $workflow->dependencies()->key($key));
        }
        $this->expectException(OverflowException::class);
        $workflow->withAdded(
            step2: new Step(WorkflowTestStepDeps1::class)
        );
    }
}
