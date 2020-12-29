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

use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Response\ResponseInterface;

/**
 * Describes the component in charge of defining a workflow run, with the arguments returned for each task.
 */
interface WorkflowRunInterface
{
    public function __construct(WorkflowInterface $workflow, mixed ...$namedArguments);

    /**
     * Provides access to workflow uuid V4 (RFC 4122).
     * https://tools.ietf.org/html/rfc4122
     */
    public function uuid(): string;

    /**
     * Provides access to the WorkflowInterface instance.
     */
    public function workflow(): WorkflowInterface;

    /**
     * Provides access to the ArgumentsInterface instance.
     */
    public function arguments(): ArgumentsInterface;

    /**
     * @throws ArgumentCountException
     */
    public function withStepResponse(string $step, ResponseInterface $response): self;

    /**
     * Indicates whether the instance has the given `$step`. Will return `true` if step has ran.
     */
    public function has(string $step): bool;

    /**
     * Provides access to the ResponseInterface instance for the given `$step`.
     */
    public function get(string $step): ResponseInterface;
}
