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
use Chevere\Components\Response\Response;
use Chevere\Components\Workflow\Step;
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
        $workflow = (new Workflow())
            ->withAdded(
                steps: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${foo}'
                )
            );
        $arguments = [
            'foo' => 'bar',
        ];
        $workflowRun = new WorkflowRun($workflow, ...$arguments);
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
        $workflow = (new Workflow())
            ->withAdded(
                step0: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${foo}'
                ),
                step1: new Step(
                    WorkflowRunTestStep2::class,
                    foo: '${step0:response0}',
                    bar: '${bar}'
                )
            );
        $arguments = [
            'foo' => 'hola',
            'bar' => 'mundo',
        ];
        $responseData = [
            'response0' => 'value',
        ];
        $workflowRun = (new WorkflowRun($workflow, ...$arguments))
            ->withStepResponse(
                'step0',
                (new Response())
                    ->withData(...$responseData)
            );
        $this->assertTrue($workflow->hasVar('${step0:response0}'));
        $this->assertTrue($workflowRun->has('step0'));
        $this->assertSame($responseData, $workflowRun->get('step0')->data());
    }

    public function testWithAddedNotFound(): void
    {
        $workflow = (new Workflow())
            ->withAdded(
                step0: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${foo}'
                )
            );
        $arguments = [
            'foo' => 'hola',
        ];
        $this->expectException(OutOfBoundsException::class);
        (new WorkflowRun($workflow, ...$arguments))
            ->withStepResponse(
                'not-found',
                new Response()
            );
    }

    public function testWithAddedMissingArguments(): void
    {
        $workflow = (new Workflow())
            ->withAdded(
                step0: new Step(WorkflowRunTestStep0::class),
                step1: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${step0:response0}'
                )
            );
        $this->expectException(ArgumentCountException::class);
        (new WorkflowRun($workflow))
            ->withStepResponse(
                'step0',
                new Response()
            );
    }
}

class WorkflowRunTestStep0 extends Action
{
    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}

class WorkflowRunTestStep1 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return new Parameters(foo: new StringParameter());
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}

class WorkflowRunTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return new Parameters(
            foo: new StringParameter(),
            bar: new StringParameter()
        );
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return $this->getResponse();
    }
}
