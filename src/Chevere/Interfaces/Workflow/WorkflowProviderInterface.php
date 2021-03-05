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

namespace Chevere\Interfaces\Workflow;

use Chevere\Exceptions\Core\LogicException;

/**
 * Describes the component in charge of providing Workflow.
 */
interface WorkflowProviderInterface
{
    /**
     * Defines the Workflow.
     */
    public function getWorkflow(): WorkflowInterface;

    /**
     * Return an instance with the specified Workflow.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified Workflow.
     */
    public function withWorkflow(WorkflowInterface $workflow): static;

    /**
     * Provides access to the Workflow instance.
     */
    public function workflow(): WorkflowInterface;

    /**
     * @throws LogicException
     */
    public function assertWorkflow(): void;
}
