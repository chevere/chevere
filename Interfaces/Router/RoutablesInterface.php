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

namespace Chevere\Interfaces\Router;

use Chevere\Interfaces\DataStructures\DsMapInterface;

interface RoutablesInterface extends DsMapInterface
{
    public function withPut(RoutableInterface $routable): RoutablesInterface;

    public function hasKey(string $name): bool;

    public function get(string $name): RoutableInterface;
}
