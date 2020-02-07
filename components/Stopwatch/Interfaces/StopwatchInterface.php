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

namespace Chevere\Components\Stopwatch\Interfaces;

use BadMethodCallException;

interface StopwatchInterface
{
    /**
     * Marks a time.
     *
     * @param string $name A name for this mark.
     */
    public function mark(string $name): void;

    /**
     * Stop the watch, must be called before accesing records.
     */
    public function stop(): void;

    /**
     * Provides access to the records array, each one relative to the previous record.
     *
     * @return array [id => $timeElapsed] high-resolution times
     * @throws BadMethodCallException if called before stop.
     */
    public function records(): array;

    /**
     * Provides access to the records readable array, each one relative to the previous record.
     *
     * @return array [id => $timeElapsedRead] readable times, like `100ms`
     * @throws BadMethodCallException if called before stop.
     *
     * Note that $timeElapsedRead is relative to previous record.
     */
    public function recordsRead(): array;

    /**
     * @return int High-resolution time.
     * @throws BadMethodCallException if called before stop.
     */
    public function timeElapsed(): int;

    /**
     * @return string The time elapsed, in miliseconds with its unit like `100 ms`
     * @throws BadMethodCallException if called before stop.
     */
    public function timeElapsedRead(): string;
}
