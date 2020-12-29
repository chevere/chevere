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

namespace Chevere\Components\Workflow;

use Chevere\Components\Response\Traits\ResponseTrait;
use Chevere\Interfaces\Workflow\WorkflowMessageInterface;
use Chevere\Interfaces\Workflow\WorkflowResponseInterface;

final class WorkflowResponse implements WorkflowResponseInterface
{
    use ResponseTrait;

    private WorkflowMessageInterface $workflowMessage;

    public function withWorkflowMessage(WorkflowMessageInterface $workflowMessage): self
    {
        $new = clone $this;
        $new->workflowMessage = $workflowMessage;

        return $new;
    }

    public function workflowMessage(): WorkflowMessageInterface
    {
        return $this->workflowMessage;
    }
}
