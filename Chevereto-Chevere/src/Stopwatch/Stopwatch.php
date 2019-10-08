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

namespace Chevere\Stopwatch;

use InvalidArgumentException;
use Chevere\Message\Message;
use Chevere\Time\Time;

/**
 * A simple stopwatch, useful for userland execution time measurement.
 */
final class Stopwatch
{
    /** @var array */
    private $marks;

    /** @var array [id => $timeElapsedRead relative to previous record ] */
    private $records;

    /** @var float */
    private $timeStart;

    /** @var float */
    private $timeEnd;

    /** @var float Nanotime */
    private $timeElapsed;

    /** @var string The time elapsed, in miliseconds with tis unit (100 ms) */
    private $timeElapsedRead;

    /** @var array [id => $flagName] */
    private $index;

    /** @var float Time consumed by record checks */
    private $gap;

    public function __construct()
    {
        $this->marks[] = 0;
        $this->index[] = 'start';
        $this->gap = 0;
        $this->timeStart = hrtime(true);
    }

    public function record(string $flagName): void
    {
        $then = hrtime(true);
        if ('stop' == $flagName) {
            throw new InvalidArgumentException(
                (new Message('Use of reserved flag name %flagName%.'))
                    ->code('%flagName%', 'stop')
                    ->toString()
            );
        }
        if (in_array($flagName, $this->index)) {
            throw new InvalidArgumentException(
                (new Message('Flag name %flagName% has be already registered, you must use an unique flag for each %className% instance.'))
                    ->code('%flagName%', $flagName)
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
        $this->index[] = $flagName;
        $now = hrtime(true);
        $this->gap += $now - $then;
        $this->marks[] = $now - $this->gap;
    }

    public function stop(): void
    {
        $this->timeEnd = hrtime(true);
        $this->timeElapsed = $this->timeEnd - $this->timeStart - $this->gap;
        $this->timeElapsedRead = Time::nanoToRead($this->timeElapsed);
        $prevMicrotime = 0;
        $this->index[] = 'stop';
        $this->marks[] = $this->timeEnd;
        $this->records = [];
        foreach ($this->marks as $id => $microtime) {
            $time = $microtime - $prevMicrotime;
            $this->records[$this->index[$id]] = Time::nanoToRead($time);
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
}
