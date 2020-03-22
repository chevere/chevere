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
     * Return an instance with the specified added $group and $routeName.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added $group and $routeName.
     *
     * @param string $group The group name
     * @param string $routeName A route name associated to $group
     */
    public function withAdded(string $group, string $routeName): RouterGroupsInterface;

    public function has(string $group): bool;

    /**
     * @return array An array containing the routes for $group [routeName,]
     */
    public function get(string $group): array;

    public function getForRouteName(string $routeName): string;

    public function toArray(): array;
}
