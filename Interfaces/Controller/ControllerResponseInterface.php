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

namespace Chevere\Interfaces\Controller;

interface ControllerResponseInterface
{
    public function withData(array $data): ControllerResponseInterface;

    public function isSuccess(): bool;

    public function data(): array;
}
