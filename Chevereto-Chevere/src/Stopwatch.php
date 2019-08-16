<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere;

use InvalidArgumentException;

/**
 * A simple stopwatch, useful for userland execution time measurement.
 */
final class Stopwatch
{
    /** @var array [flag => $timeElapsedRead relative to previous record ] */
    private $records;

    /** @var float */
    private $timeStart;

    /** @var float */
    private $timeEnd;

    /** @var float Microtime */
    private $timeElapsed;

    /** @var string The time elapsed, in miliseconds with tis unit (100 ms) */
    private $timeElapsedRead;

    public function __construct()
    {
        $this->timeStart = microtime(true);
        $this->records = [];
    }

    public function record(string $flagName): void
    {
        if ('' == $flagName) {
            throw new InvalidArgumentException('You must indicate the flag name');
        }
        $this->records[$flagName] = $this->microtimeToRead(microtime(true) - $this->timeStart);
    }

    public function stop(): string
    {
        $this->timeEnd = microtime(true);
        $this->timeElapsed = $this->timeEnd - $this->timeStart;
        $this->timeElapsedRead = $this->microtimeToRead($this->timeElapsed);

        return $this->timeElapsedRead;
    }

    public function records(): array
    {
        return $this->records;
    }

    public function timeElapsed(): float
    {
        return $this->timeElapsed;
    }

    public function timeElapsedRead(): string
    {
        return $this->timeElapsedRead;
    }

    private function microtimeToRead(float $microtime): string
    {
        return round($microtime * 1000, 2).' ms';
    }
}
