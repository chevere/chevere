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

interface RunnerContract
{
    /**
     * Creates a new RunnerContract instance by passing the builder to run.
     */
    public function __construct(BuilderContract $builder);

    /**
     * Provides access to the BuilderContract instance.
     */
    public function builder(): BuilderContract;

    /**
     * Return an instance with a console loop.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the console loop flag.
     *
     * The console loop is needed to stop an endless loop when doing CLI instructions.
     */
    public function withConsoleLoop(): RunnerContract;

    /**
     * Returns a boolean indicating whether the instance has a console loop.
     */
    public function hasConsoleLoop(): bool;

    /**
     * Returns a boolean indicating whether the instance has route not found.
     */
    public function hasRouteNotFound(): bool;

    /**
     * This method runs the application, CLI aware.
     */
    public function withRun(): RunnerContract;
}
