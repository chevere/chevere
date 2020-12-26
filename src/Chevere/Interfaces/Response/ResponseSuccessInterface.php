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

namespace Chevere\Interfaces\Response;

use Chevere\Interfaces\Workflow\WorkflowMessageInterface;

/**
 * Describes the component in charge of defining a success response.
 */
interface ResponseSuccessInterface extends ResponseInterface
{
    /**
     * Return an instance with the specified workflow message.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified workflow message.
     */
    public function withWorkflowMessage(WorkflowMessageInterface $workflowMessage): self;

    public function workflowMessage(): WorkflowMessageInterface;
}
