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
    /**
     * Return an instance with the specified added group and route id.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added group and route id.
     *
     * @param string $group The group name
     * @param int $id A route id associated to $group
     */
    public function withAdded(string $group, int $id): RouterGroupsInterface;

    public function has(string $group): bool;

    public function get(string $group): array;

    public function getForId(int $id): string;

    public function toArray(): array;
}
