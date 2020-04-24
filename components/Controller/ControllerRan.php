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

namespace Chevere\Components\Controller;

use Chevere\Components\Controller\Interfaces\ControllerRanInterface;
use Throwable;

final class ControllerRan implements ControllerRanInterface
{
    private int $code = 0;

    private array $data;

    private Throwable $throwable;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function withThrowable(Throwable $throwable): ControllerRanInterface
    {
        $new = clone $this;
        $new->throwable = $throwable;
        $new->code = 1;

        return $new;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function hasThrowable(): bool
    {
        return isset($this->throwable);
    }

    public function throwable(): Throwable
    {
        return $this->throwable;
    }
}
