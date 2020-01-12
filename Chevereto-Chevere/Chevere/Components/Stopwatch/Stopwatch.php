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

namespace Chevere\Components\Stopwatch;

use BadMethodCallException;
use InvalidArgumentException;
use Chevere\Components\Message\Message;
use Chevere\Components\Stopwatch\Interfaces\StopwatchInterface;
use Chevere\Components\Time\TimeHr;

/**
 * A simple stopwatch, useful for userland execution time measurement.
 */
final class Stopwatch implements StopwatchInterface
{
    private array $marks;

    /** @var array [pos => $name] */
    private array $index;

    /** @var int High-resolution time consumed by record checks */
    private int $gap = 0;

    /** @var array [id => $timeElapsed] */
    private array $records;

    /** @var array [id => $timeElapsedRead] */
    private array $recordsRead;

    /** @var int High-resolution time */
    private int $timeStart;

    /** @var int High-resolution time */
    private int $timeEnd;

    /** @var int High-resolution time */
    private int $timeElapsed;

    /** @var string The time elapsed, in miliseconds with its unit like `100 ms` */
    private string $timeElapsedRead;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->timeStart = (int) hrtime(true);
        $this->index = ['start'];
        $this->marks = [$this->timeStart];
    }

    /**
     * {@inheritdoc}
     */
    public function mark(string $name): void
    {
        $then = (int) hrtime(true);
        if ('stop' == $name) {
            throw new InvalidArgumentException(
                (new Message('Use of reserved flag name %flagName%'))
                    ->code('%flagName%', 'stop')
                    ->toString()
            );
        }
        if (in_array($name, $this->index)) {
            throw new InvalidArgumentException(
                (new Message('Flag name %flagName% has be already registered, you must use an unique flag for each mark'))
                    ->code('%flagName%', $name)
                    ->toString()
            );
        }
        $this->index[] = $name;
        $now = (int) hrtime(true);
        $this->gap += $now - $then;
        $this->marks[] = $now - $this->gap;
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): void
    {
        $this->timeEnd = (int) hrtime(true);
        $this->index[] = 'stop';
        $this->marks[] = $this->timeEnd;
        $this->records = [];
        $this->recordsRead = [];
        $prevMicrotime = $this->marks[0];
        foreach ($this->marks as $id => $microtime) {
            $time = $microtime - $prevMicrotime;
            $this->records[$this->index[$id]] = $time;
            $this->recordsRead[$this->index[$id]] = (new TimeHr($time))->toReadMs();
            $prevMicrotime = $microtime;
        }
        $this->timeElapsed = array_sum(array_values($this->records));
        $this->timeElapsedRead = (new TimeHr($this->timeElapsed))->toReadMs();
    }

    /**
     * {@inheritdoc}
     */
    public function records(): array
    {
        $this->assertMethodCall(__METHOD__);

        return $this->records;
    }

    /**
     * {@inheritdoc}
     */
    public function recordsRead(): array
    {
        $this->assertMethodCall(__METHOD__);

        return $this->recordsRead;
    }

    /**
     * {@inheritdoc}
     */
    public function timeElapsed(): int
    {
        $this->assertMethodCall(__METHOD__);

        return $this->timeElapsed;
    }

    /**
     * {@inheritdoc}
     */
    public function timeElapsedRead(): string
    {
        $this->assertMethodCall(__METHOD__);

        return $this->timeElapsedRead;
    }

    private function assertMethodCall(string $method): void
    {
        if (!isset($this->records)) {
            throw new BadMethodCallException(
                (new Message('The method %method% must be called after calling the %stop% method'))
                    ->code('%method%', $method)
                    ->code('%before%', __CLASS__ . '::stop')
                    ->toString()
            );
        }
    }
}
