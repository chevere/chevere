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

interface RunnerInterface
{
    public function __construct(BuilderInterface $builder);

    /**
     * Provides access to the BuilderInterface instance.
     */
    public function builder(): BuilderInterface;

    /**
     * Return an instance with a console loop.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the console loop flag.
     *
     * The console loop is needed to stop an endless loop when doing CLI instructions.
     */
    public function withConsoleLoop(): RunnerInterface;

    /**
     * Returns a boolean indicating whether the instance has a console loop.
     */
    public function hasConsoleLoop(): bool;

    /**
     * This method runs the application, CLI aware.
     */
    public function withRun(): RunnerInterface;
}
