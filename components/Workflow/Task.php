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

use Chevere\Interfaces\Workflow\TaskInterface;

final class Task implements TaskInterface
{
    private string $callable;

    private array $arguments;

    public function __construct(string $callable)
    {
        $this->callable = $callable;
        $this->arguments = [];
    }

    public function withArguments(string ...$arguments): TaskInterface
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }

    public function callable(): string
    {
        return $this->callable;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
