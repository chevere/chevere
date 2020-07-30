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

namespace Chevere\Interfaces\Job;

/**
 * Describes the component in charge of defining a workflow run, with the arguments returned for each task.
 */
interface WorkflowRunInterface
{
    public function __construct(WorkflowInterface $workflow);

    public function with(string $taskName, string ...$arguments);

    public function get(string $taskName): TaskInterface;
}
