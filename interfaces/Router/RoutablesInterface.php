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

use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\DataStructures\DsMapInterface;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `RoutableInterface`.
 */
interface RoutablesInterface extends DsMapInterface
{
    /**
     * Return an instance with the specified `$routable`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$routable`.
     */
    public function withPut(RoutableInterface $routable): RoutablesInterface;

    /**
     * Indicates whether the instance has a routable identified by its `$name`.
     */
    public function has(string $name): bool;

    /**
     * Returns the routable identified by its `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function get(string $name): RoutableInterface;

    /**
     * @return Generator<string, RoutableInterface>
     */
    public function getGenerator(): Generator;
}
