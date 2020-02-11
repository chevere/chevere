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

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\Middleware\Exceptions\MiddlewareNamesEmptyException;

interface MiddlewareRunnerInterface
{
    /**
     * Creates a new instance.
     *
     * @param MiddlewareNameCollectionInterface $middlewareNameCollection An instance containing at least one middleware
     * @param AppInterface                      $app                      an application container with a RequestInterface
     *
     * @throws AppWithoutRequestException    if the $app doesn't contain a RequestInterface
     * @throws MiddlewareNamesEmptyException if the $middlewareNames are empty
     */
    public function __construct(MiddlewareNameCollectionInterface $middlewareNameCollection, AppInterface $app);

    /**
     * Return an instance with the run property.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the run property.
     */
    public function withRun(): MiddlewareRunnerInterface;

    /**
     * Returns a boolean indicating whether the instance has run.
     */
    public function hasRun(): bool;

    /**
     * Provides access to the run record.
     */
    public function record(): array;
}
