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

interface RouterGroupsInterface
{
    public function withAdded(string $group, int $id): RouterGroupsInterface;

    public function has(string $group): bool;

    public function get(string $group): array;

    public function getForId(int $id): string;

    public function toArray(): array;
}
