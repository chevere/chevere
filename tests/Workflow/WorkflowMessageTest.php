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

use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowMessage;
use Chevere\Components\Workflow\WorkflowRun;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;
use PHPUnit\Framework\TestCase;

final class WorkflowMessageTest extends TestCase
{
    public function getWorkflowRun(): WorkflowRunInterface
    {
        return new WorkflowRun(new Workflow('test'), []);
    }

    public function testConstruct(): void
    {
        $run = $this->getWorkflowRun();
        $queue = new WorkflowMessage($run);
        $this->assertSame(0, $queue->priority());
        $this->assertSame($run, $queue->workflowRun());
        $this->assertIsString($queue->uuid());
        $this->assertSame(0, $queue->expiration());
    }

    public function testWithPriority(): void
    {
        $queue = new WorkflowMessage($this->getWorkflowRun());
        $priority = 255;
        $queue = $queue->withPriority($priority);
        $this->assertSame($priority, $queue->priority());
        $this->expectException(InvalidArgumentException::class);
        $queue = $queue->withPriority(-999);
    }

    public function testWithDelay(): void
    {
        $queue = new WorkflowMessage($this->getWorkflowRun());
        $delay = 3600;
        $queue = $queue->withDelay($delay);
        $this->assertSame($delay, $queue->delay());
        $this->expectException(InvalidArgumentException::class);
        $queue = $queue->withDelay(-3600);
    }

    public function testWithExpiresInterval(): void
    {
        $queue = new WorkflowMessage($this->getWorkflowRun());
        $ahead = 60;
        $queue = $queue->withExpiration($ahead);
        $this->assertSame($ahead, $queue->expiration());
        $this->expectException(InvalidArgumentException::class);
        $queue = $queue->withExpiration(-60);
    }
}
