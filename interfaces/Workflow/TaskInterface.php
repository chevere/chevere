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
interface TaskInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $action);

    public function withArguments(string ...$arguments): TaskInterface;

    public function action(): string;

    /**
     * @return string[]
     */
    public function arguments(): array;
}
