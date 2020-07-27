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

namespace Chevere\Components\Job;

use Chevere\Interfaces\Job\TaskInterface;

final class Task implements TaskInterface
{
    private string $name;

    private string $callable;

    private array $arguments;

    public function __construct(string $name, string $callable, array $arguments)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->arguments = $arguments;
    }

    public function name(): string
    {
        return $this->name;
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
