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
 * Describes the component in charge of defining a provisional response.
 */
interface ResponseProvisionalInterface extends ResponseInterface
{
    /**
     * Return an instance with the specified data.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified data.
     */
    public function withData(array $data): ResponseProvisionalInterface;

    /**
     * Return an instance with the specified workflow message.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified workflow message.
     */
    public function withWorkflowMessage(WorkflowMessageInterface $workflowMessage): ResponseProvisionalInterface;

    public function workflowMessage(): WorkflowMessageInterface;
}
