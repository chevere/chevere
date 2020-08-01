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

use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;

/**
 * Describes the component in charge of defining a workflow run, with the arguments returned for each task.
 */
interface WorkflowRunInterface
{
    public function __construct(WorkflowInterface $workflow, ArgumentsInterface $arguments);

    public function workflow(): WorkflowInterface;

    public function arguments(): ArgumentsInterface;

    public function withAdded(string $step, ResponseSuccessInterface $response): WorkflowRunInterface;

    public function has(string $step): bool;

    public function get(string $step): array;
}
