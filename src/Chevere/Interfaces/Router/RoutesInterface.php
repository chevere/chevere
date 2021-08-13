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
use Chevere\Interfaces\DataStructure\MappedInterface;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `RouteInterface`.
 */
interface RoutesInterface extends MappedInterface
{
    public const EXCEPTION_CODE_TAKEN_NAME = 110;

    public const EXCEPTION_CODE_TAKEN_PATH = 100;

    /**
     * Return an instance with the specified `$namedRoutes`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$namedRoutes`.
     */
    public function withAdded(RouteInterface ...$namedRoutes): self;

    /**
     * Indicates whether the instance has routable(s) identified by its `$path`.
     */
    public function has(string ...$path): bool;

    /**
     * Returns the routable identified by its `$path`.
     *
     * @throws OutOfBoundsException
     */
    public function get(string $path): RouteInterface;

    /**
     * @return Generator<string, RouteInterface>
     */
    public function getGenerator(): Generator;
}
