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

namespace Chevere\Components\Router\Interfaces;

interface RouterIndexInterface
{
    public function has(string $key): bool;

    public function withAdded(string $key, int $id, string $group, string $name): RouterIndexInterface;

    public function toArray(): array;
}
