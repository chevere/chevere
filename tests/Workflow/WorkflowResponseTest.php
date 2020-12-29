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

use function Chevere\Components\Workflow\getWorkflowMessage;
use Chevere\Components\Workflow\Workflow;
use Chevere\Components\Workflow\WorkflowResponse;
use PHPUnit\Framework\TestCase;

final class WorkflowResponseTest extends TestCase
{
    public function testWithWorkflowMessage(): void
    {
        $data = [];
        $workflowMessage = getWorkflowMessage(new Workflow('name'))
            ->withDelay(123)
            ->withExpiration(111)
            ->withPriority(10);
        $response = (new WorkflowResponse())
            ->withWorkflowMessage($workflowMessage);
        $this->assertSame($workflowMessage, $response->workflowMessage());
        $this->assertSame($data, $response->data());
    }
}
