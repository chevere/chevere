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

namespace Chevere\Components\Workflow\Traits;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Workflow\WorkflowInterface;

trait WorkflowProviderTrait
{
    protected WorkflowInterface $workflow;

    abstract public function getWorkflow(): WorkflowInterface;

    public function withWorkflow(WorkflowInterface $workflow): static
    {
        $new = clone $this;
        $new->workflow = $workflow;

        return $new;
    }

    public function workflow(): WorkflowInterface
    {
        return $this->workflow;
    }

    public function assertWorkflow(): void
    {
        if (! isset($this->workflow)) {
            throw new LogicException(message: new Message('Missing Workflow instance'));
        }
    }
}
