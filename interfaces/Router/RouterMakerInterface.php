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

use Chevere\Exceptions\Router\RouteKeyConflictException;
use Chevere\Exceptions\Router\RouteNameConflictException;
use Chevere\Exceptions\Router\RoutePathExistsException;
use Chevere\Exceptions\Router\RouterMakerException;

interface RouterMakerInterface
{
    public function __construct();

    /**
     * Return an instance with the specified added RoutableInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added RoutableInterface.
     *
     * @throws RouterMakerException       if unable to process routing
     * @throws RoutePathExistsException   if $routable has been already routed
     * @throws RouteKeyConflictException  if $routable conflicts with other RoutableInterface
     * @throws RouteNameConflictException if $routable name conflicts with other RoutableInterface
     */
    public function withAddedRoutable(RoutableInterface $routable, string $group): RouterMakerInterface;

    /**
     * Provides access to the generated RouterInterface instance.
     */
    public function router(): RouterInterface;
}
