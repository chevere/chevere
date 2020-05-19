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

use Chevere\Interfaces\Controller\ControllerResponseInterface;

final class ControllerResponse implements ControllerResponseInterface
{
    private bool $isSuccess;

    private array $data = [];

    public function __construct(bool $isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    public function withData(array $data): ControllerResponseInterface
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function data(): array
    {
        return $this->data;
    }
}
