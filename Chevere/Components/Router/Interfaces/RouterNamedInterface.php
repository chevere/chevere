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

interface RouterNamedInterface
{
    public function withAdded(string $name, int $id): RouterNamedInterface;

    public function has(string $name): bool;

    public function get(string $name): int;

    public function getForId(int $id): string;

    public function toArray(): array;
}
