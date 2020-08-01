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

use Chevere\Components\Workflow\Task;
use Chevere\Components\Workflow\Workflow;
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
        $task = new Task('callable');
        $workflow = (new Workflow('test-workflow'))
            ->withAdded('task', $task)
            ->withAddedBefore('task', 'task-before', $task);
        $this->assertSame([
            'task-before',
            'task'
        ], $workflow->order());
        $workflow = $workflow
            ->withAddedAfter('task-before', 'task-after', $task);
        $this->assertSame([
            'task-before',
            'task-after',
            'task'
        ], $workflow->order());
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
                $task->withArguments('test-argument', '${foo}')
            );
        $this->assertTrue($workflow->parameters()->has('foo'));
        $workflow = $workflow
            ->withAdded(
                'next-step',
                $task->withArguments('${step:foo}', '${foo}', '${bar}')
            );
        $this->assertTrue($workflow->hasReference('${step:foo}'));
        $this->assertSame(['step', 'foo'], $workflow->getReference('${step:foo}'));
        $this->assertTrue($workflow->parameters()->has('foo'));
        $this->assertTrue($workflow->parameters()->has('bar'));
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
    //                 ->withArguments('${filename}')
    //         )
    //         ->withAdded(
    //             'upload',
    //             (new Task('uploadImageFn'))
    //                 ->withArguments('${filename}')
    //         )
    //         ->withAdded(
    //             'bind-user',
    //             (new Task('bindImageToUserFn'))
    //                 ->withArguments('${upload:id}', '${userId}')
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
    //                 ->withArguments('${filename}')
    //         )
    //         // Plugin: sepia filter
    //         ->withAddedAfter(
    //             'validate',
    //             'vendor-sepia-filter',
    //             (new Task('vendorPath/sepiaFilter'))
    //                 ->withArguments('${filename}')
    //         );
    // }
}
