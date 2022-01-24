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

use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Workflow\Step;
use Chevere\Workflow\Steps;
use Chevere\Workflow\Workflow;
use Chevere\Workflow\WorkflowRun;
use PHPUnit\Framework\TestCase;

final class WorkflowRunTest extends TestCase
{
    public function testConstruct(): void
    {
        $workflow = (new Workflow(new Steps()))
            ->withAddedStep(
                steps: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${foo}',
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

    public function testWithStepResponse(): void
    {
        $workflow = (new Workflow(new Steps()))
            ->withAddedStep(
                step0: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${foo}'
                ),
                step1: new Step(
                    WorkflowRunTestStep2::class,
                    foo: '${baz}',
                    bar: '${bar}'
                )
            );
        $arguments = [
            'foo' => 'hola',
            'bar' => 'mundo',
            'baz' => 'ql',
        ];
        $workflowRun = (new WorkflowRun($workflow, ...$arguments));
        $workflowRunWithStepResponse = $workflowRun
            ->withStepResponse('step0', new Response());
        $this->assertNotSame($workflowRun, $workflowRunWithStepResponse);
        $this->assertTrue($workflow->vars()->has('${foo}'));
        $this->assertTrue($workflow->vars()->has('${baz}'));
        $this->assertTrue($workflowRunWithStepResponse->has('step0'));
        $this->assertSame([], $workflowRunWithStepResponse->get('step0')->data());
        $this->expectException(ArgumentCountError::class);
        $workflowRunWithStepResponse
            ->withStepResponse('step0', new Response(extra: 'not-allowed'));
    }

    public function testWithAddedNotFound(): void
    {
        $workflow = (new Workflow(new Steps()))
            ->withAddedStep(
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
        $workflow = (new Workflow(new Steps()))
            ->withAddedStep(
                step0: new Step(WorkflowRunTestStep0::class),
                step1: new Step(
                    WorkflowRunTestStep1::class,
                    foo: '${foo}'
                )
            );
        $this->expectException(ArgumentCountError::class);
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
