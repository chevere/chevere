<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\App;

use Chevere\Contracts\Middleware\MiddlewareNamesContract;
use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\Middleware\Exceptions\MiddlewareNamesEmptyException;

interface MiddlewareRunnerContract
{
    /**
     * Creates a new instance.
     *
     * @param MiddlewareNamesContract $middlewareNames An instance containing at least one middleware
     * @param AppContract             $app             an application container with a RequestContract
     *
     * @throws AppWithoutRequestException    if the $app doesn't contain a RequestContract
     * @throws MiddlewareNamesEmptyException if the $middlewareNames are empty
     */
    public function __construct(MiddlewareNamesContract $middlewareNames, AppContract $app);

    /**
     * Return an instance with the run property.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the run property.
     */
    public function withRun(): MiddlewareRunnerContract;

    /**
     * Returns a boolean indicating whether the instance has run.
     */
    public function hasRun(): bool;

    /**
     * Provides access to the run record.
     */
    public function record(): array;
}
