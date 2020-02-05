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

use Chevere\Components\Router\Exceptions\RoutePathExistsException;
use Chevere\Components\Router\Exceptions\RouteKeyConflictException;
use Chevere\Components\Router\Exceptions\RouteNameConflictException;
use Chevere\Components\Router\Exceptions\RouterMakerException;

interface RouterMakerInterface
{
    /**
     * Return an instance with the specified added RouteableInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added RouteableInterface.
     *
     * @throws RouterMakerException       if unable to process routing
     * @throws RoutePathExistsException   if $routeable has been already routed
     * @throws RouteKeyConflictException  if $routeable conflicts with other RouteableInterface
     * @throws RouteNameConflictException if $routeable name conflicts with other RouteableInterface
     */
    public function withAddedRouteable(RouteableInterface $routeable, string $group): RouterMakerInterface;

    /**
     * Provides access to the generated RouterInterface instance.
     */
    public function router(): RouterInterface;
}
