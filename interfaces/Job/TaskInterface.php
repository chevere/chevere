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
 * Describes the component in charge of defining a task (a unit of job).
 */
interface TaskInterface
{
    public function __construct(string $name, string $callable, array $arguments);

    public function name(): string;

    public function callable(): string;

    public function arguments(): array;
}
