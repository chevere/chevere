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

use Chevere\Exceptions\Core\InvalidArgumentException;

/**
 * Describes the component in charge of defining a task (a unit of job).
 */
interface StepInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $action);

    /**
     * Return an instance with the specified named `$namedArguments`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified named `$namedArguments`.
     */
    public function withArguments(mixed ...$namedArguments): self;

    public function action(): string;

    /**
     * @return string[]
     */
    public function arguments(): array;
}
