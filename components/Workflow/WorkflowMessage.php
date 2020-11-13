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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Workflow\WorkflowMessageInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;

final class WorkflowMessage implements WorkflowMessageInterface
{
    private WorkflowRunInterface $workflowRun;

    private string $token;

    private int $priority;

    private int $expiration;

    public function __construct(WorkflowRunInterface $workflowRun)
    {
        $this->workflowRun = $workflowRun;
        $this->token = bin2hex(random_bytes(128));
        $this->priority = 0;
        $this->expiration = 0;
    }

    public function withPriority(int $priority): WorkflowMessageInterface
    {
        if ($priority < 0 || $priority > 255) {
            throw new InvalidArgumentException(
                (new Message('Expecting a priority value in the integer range %range%, value %provided% provided'))
                    ->strong('%range%', '0,255')
                    ->code('%provided%', (string) $priority)
            );
        }
        $new = clone $this;
        $new->priority = $priority;

        return $new;
    }

    public function withExpiration(int $milliseconds): WorkflowMessageInterface
    {
        $new = clone $this;
        $new->expiration = $milliseconds;

        return $new;
    }

    public function workflowRun(): WorkflowRunInterface
    {
        return $this->workflowRun;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function expiration(): int
    {
        return $this->expiration;
    }
}
