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

namespace Chevere\Tests\Job;

use Chevere\Components\Job\Task;
use Chevere\Components\Job\Workflow;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use PHPUnit\Framework\TestCase;

final class WorkflowTest extends TestCase
{
    public function testConstruct(): void
    {
        $name = 'test-workflow';
        $workflow = new Workflow($name);
        $this->assertMatchesRegularExpression('/^(.*)\.(\d*)@(\d*)$/', $workflow->id());
        $this->assertSame($name, $workflow->name());
        $this->assertCount(0, $workflow);
    }

    public function testWithAdded(): void
    {
        $workflow = new Workflow('test-workflow');
        $task = new Task('callable');
        $taskName = 'task';
        $workflow = $workflow->withAdded($taskName, $task);
        $this->assertCount(1, $workflow);
        $this->assertSame([$taskName], $workflow->keys());
        $this->expectException(OverflowException::class);
        $workflow->withAdded($taskName, $task);
    }

    public function testWithAddedBeforeAndAfter(): void
    {
        $task = new Task('callable');
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('task', $task)
            ->withAddedBefore('task', 'task-before', $task);
        $this->assertSame([
            'task-before',
            'task'
        ], $workflow->keys());
        $workflow = $workflow
            ->withAddedAfter('task-before', 'task-after', $task);
        $this->assertSame([
            'task-before',
            'task-after',
            'task'
        ], $workflow->keys());
    }

    public function testWithAddedBeforeOutOfBounds(): void
    {
        $task = new Task('callable');
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('found', $task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedBefore('not-fond', 'test', $task);
    }

    public function testWithAddedAfterOutOfBounds(): void
    {
        $task = new Task('callable');
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('found', $task);
        $this->expectException(OutOfBoundsException::class);
        $workflow->withAddedAfter('not-fond', 'test', $task);
    }

    public function testWithAddedTaskWithArguments(): void
    {
        $task = (new Task('callable'))
            ->withArguments('foo', 'bar');
        $name = 'name';
        $workflow = (new Workflow('test-workflow'))->withAdded($name, $task);
        $this->assertSame($task, $workflow->get($name));
    }

    public function testWithAddedTaskWithReferenceArguments(): void
    {
        $task = new Task('callable');
        $workflow = (new Workflow('test-workflow'))
            ->withAdded(
                'step',
                $task->withArguments('test-argument', '${job:foo}', '${job:bar}')
            );
        $this->assertSame(
            [
                'foo', 'bar'
            ],
            $workflow->getParameters('job')
        );
        $this->assertSame(
            [
                'test-argument', ['job', 'foo'], ['job', 'bar']
            ],
            $workflow->getParameters('step')
        );
        $workflow = $workflow
            ->withAdded(
                'next-step',
                $task->withArguments('${step:foo}', '${job:bar}', '${job:oof}')
            );
        $this->assertSame(
            [
                ['step', 'foo'], ['job', 'bar'], ['job', 'oof']
            ],
            $workflow->getParameters('next-step')
        );
        $this->assertSame(
            [
                'foo', 'bar', 'oof'
            ],
            $workflow->getParameters('job')
        );
        $this->expectException(InvalidArgumentException::class);
        $workflow->withAdded(
            'missing-reference',
            (new Task('callable'))->withArguments('${not:found}')
        );
    }

    // public function testTookOurJobs(): void
    // {
    //     $this->expectNotToPerformAssertions();
    //     $workflow = (new Workflow('user-upload-image'))
    //         ->withAdded(
    //             'validate',
    //             (new Task('validateImageFn'))
    //                 ->withArguments('${job:filename}')
    //         )
    //         ->withAdded(
    //             'upload',
    //             (new Task('uploadImageFn'))
    //                 ->withArguments('${job:filename}')
    //         )
    //         ->withAdded(
    //             'bind-user',
    //             (new Task('bindImageToUserFn'))
    //                 ->withArguments('${upload:id}', '${job:userId}')
    //         )
    //         ->withAdded(
    //             'response',
    //             (new Task('picoConLaWea'))
    //                 ->withArguments('${upload:id}')
    //         );
    //     $workflow = $workflow
    //         // Plugin: check banned hashes
    //         ->withAddedBefore(
    //             'validate',
    //             'vendor-ban-check',
    //             (new Task('vendorPath/banCheck'))
    //                 ->withArguments('${job:filename}')
    //         )
    //         // Plugin: sepia filter
    //         ->withAddedAfter(
    //             'validate',
    //             'vendor-sepia-filter',
    //             (new Task('vendorPath/sepiaFilter'))
    //                 ->withArguments('${job:filename}')
    //         );
    // }
}
