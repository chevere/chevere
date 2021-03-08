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

use Chevere\Components\DataStructure\Map;
use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Workflow\Step;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Components\Workflow\WorkflowRunner;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Tests\Workflow\_resources\src\WorkflowRunnerTestDependentStep1;
use Chevere\Tests\Workflow\_resources\src\WorkflowRunnerTestDependentStep2;
use Chevere\Tests\Workflow\_resources\src\WorkflowRunnerTestStep1;
use Chevere\Tests\Workflow\_resources\src\WorkflowRunnerTestStep2;
use PHPUnit\Framework\TestCase;

final class WorkflowRunnerTest extends TestCase
{
    public function testWorkflowRunner(): void
    {
        $foo = 'hola';
        $bar = 'mundo';
        $workflow = (new Workflow())
            ->withAdded(
                step1: new Step(
                    WorkflowRunnerTestStep1::class,
                    foo: '${foo}'
                ),
                step2: new Step(
                    WorkflowRunnerTestStep2::class,
                    foo: '${step1:response1}',
                    bar: '${bar}'
                )
            );
        $arguments = [
            'foo' => $foo,
            'bar' => $bar,
        ];
        $workflowRun = new WorkflowRun($workflow, ...$arguments);
        $container = new Map();
        $workflowRunner = (new WorkflowRunner($workflowRun))
            ->withRun($container)
            ->withRun($container);
        $workflowRun = $workflowRunner->workflowRun();
        $this->assertSame($workflowRun, $workflowRunner->workflowRun());
        $action1 = new WorkflowRunnerTestStep1();
        $this->assertSame(
            $action1->run(
                $action1->getArguments(...[
                    'foo' => $foo,
                ])
            )->data(),
            $workflowRun->get('step1')->data()
        );
        $foo = $workflowRun->get('step1')->data()['response1'];
        $action2 = new WorkflowRunnerTestStep2();
        $this->assertSame(
            $action2
                ->run(
                    $action2->getArguments(...[
                        'foo' => $foo,
                        'bar' => $bar,
                    ])
                )
                ->data(),
            $workflowRun->get('step2')->data()
        );
    }

    public function testWithDependencies(): void
    {
        $foo = 'hola';
        $bar = 'mundo';
        $workflow = (new Workflow())
            ->withAdded(
                step1: new Step(
                    WorkflowRunnerTestDependentStep1::class,
                    foo: '${foo}'
                ),
                step2: new Step(
                    WorkflowRunnerTestDependentStep2::class,
                    foo: '${step1:response1}',
                    bar: '${bar}'
                )
            );
        $workflowRun = new WorkflowRun(
            $workflow,
            foo: $foo,
            bar: $bar,
        );
        $container = new Map(
            path: new Path(__FILE__),
            dir: dirForPath(__DIR__ . '/')
        );
        (new WorkflowRunner($workflowRun))
            ->withRun($container);
        $this->expectException(LogicException::class);
        (new WorkflowRunner($workflowRun))
            ->withRun(new Map());
    }
}
