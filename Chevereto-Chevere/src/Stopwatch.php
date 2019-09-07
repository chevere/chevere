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
        $this->records = [
            'start' => 0,
        ];
        $this->timeStart = microtime(true);
    }

    public function record(string $flagName): void
    {
        $now = microtime(true);
        if ('stop' == $flagName) {
            throw new InvalidArgumentException(
                (new Message('Use of reserved flag name %flagName%.'))
                    ->code('%flagName%', 'stop')
                    ->toString()
            );
        }
        if (isset($this->records[$flagName])) {
            throw new InvalidArgumentException(
                (new Message('Flag name %flagName% has be already registered, you must use an unique flag for each %className% instance.'))
                    ->code('%flagName%', $flagName)
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
        $then = microtime(true);
        $this->records[$flagName] = $then - ($now - $then);
    }

    // $this->microtimeToRead()

    public function stop(): void
    {
        $this->timeEnd = microtime(true);
        $this->timeElapsed = $this->timeEnd - $this->timeStart;
        $this->timeElapsedRead = $this->microtimeToRead($this->timeElapsed);
        $prevMicrotime = 0;
        $this->records['stop'] = $this->timeEnd;
        foreach ($this->records as $flag => $microtime) {
            $this->records[$flag] = $this->microtimeToRead($microtime - $prevMicrotime);
            $prevMicrotime = $microtime > 0 ? $microtime : $this->timeStart;
        }
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
        return number_format($microtime * 1000, 2) . ' ms';
    }
}
