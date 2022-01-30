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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionExecutedInterface;
use Throwable;

final class ActionExecuted implements ActionExecutedInterface
{
    private int $code = 0;

    private Throwable $throwable;

    public function __construct(
        private array $data
    ) {
    }

    public function code(): int
    {
        return $this->code;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function withThrowable(Throwable $throwable, int $code): ActionExecutedInterface
    {
        $new = clone $this;
        $new->throwable = $throwable;
        $new->code = $code;

        return $new;
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
