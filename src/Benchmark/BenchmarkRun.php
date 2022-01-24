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

namespace Chevere\Benchmark;

use Chevere\Benchmark\Interfaces\BenchmarkInterface;
use Chevere\Benchmark\Interfaces\BenchmarkRunInterface;
use Chevere\HrTime\HrTime;
use Chevere\Message\Interfaces\MessageInterface;
use Chevere\Message\Message;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use DateTime;
use Ds\Map;

/**
 * @codeCoverageIgnore
 * @infection-ignore-all
 */
final class BenchmarkRun implements BenchmarkRunInterface
{
    private int $timeLimit;

    private int $maxExecutionTime;

    private int $constructTime;

    private int $requestTime = 0;

    private int $times;

    private Map $records;

    private Map $results;

    private bool $isAborted;

    private bool $isPHPAborted;

    private bool $isSelfAborted;

    private int $startupTime;

    private int $timeTaken;

    private int $recordsCount;

    private int $recordsProcessed;

    private array $lines;

    private int $runs;

    private string $lineSeparator;

    private string $timeTakenReadable;

    private string $printable = '';

    /**
     * @throws ArgumentCountError if the argument count doesn't match the callable parameters
     * @throws TypeError if the argument types doesn't match
     */
    public function __construct(
        private BenchmarkInterface $benchmark
    ) {
        if (count($benchmark->index()) === 0) {
            throw new InvalidArgumentException(
                (new Message('No callable defined for object of class %className%, declare callable(s) using the %method% method'))
                    ->code('%className%', $benchmark::class)
                    ->code('%method%', $benchmark::class . '::withAddedCallable')
            );
        }
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->timeLimit = $this->maxExecutionTime;
        $this->requestTime = hrtime(true);
        $this->constructTime = (int) $this->requestTime;
        $this->times = 1;
        $this->timeTaken = 0;
        $this->records = new Map();
        $this->results = new Map();
        $this->isAborted = false;
        $this->isPHPAborted = false;
        $this->isSelfAborted = false;
        $this->recordsProcessed = 0;
    }

    public function withRequestTime(float $time): self
    {
        $new = clone $this;
        $new->requestTime = $time;

        return $new;
    }

    public function withTimes(int $times): BenchmarkRunInterface
    {
        $new = clone $this;
        $new->times = $times;

        return $new;
    }

    public function times(): int
    {
        return $this->times;
    }

    public function withTimeLimit(int $timeLimit): BenchmarkRunInterface
    {
        $new = clone $this;
        $new->timeLimit = $timeLimit;

        return $new;
    }

    public function timeLimit(): int
    {
        return $this->timeLimit;
    }

    public function exec()
    {
        $this->startupTime = (int) hrtime(true);
        $this->handleCallables();
        $this->processCallableStats();
        $title = __CLASS__ . ' results';
        $this->lineSeparator = str_repeat('-', self::COLUMNS);
        $pipe = '|';
        $pad = (int) round((self::COLUMNS - (strlen($title) + 1)) / 2, 0);
        $head = $pipe . str_repeat(' ', $pad) . $title . str_repeat(' ', floor($pad) === $pad ? $pad - 1 : $pad) . $pipe;
        $this->lines = [
            $this->lineSeparator,
            $head,
            $this->lineSeparator,
            'Start: ' . gmdate(DateTime::ATOM),
            'Hostname: ' . gethostname(),
            'PHP version: ' . PHP_VERSION,
            'Server: ' . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m'),
            $this->lineSeparator,
        ];
        $this->processResults();
        $this->handleAborted();
        $this->timeTakenReadable = ' Time taken: ' . (new HrTime($this->timeTaken))->toReadMs();
        $this->lines[] = str_repeat(' ', (int) max(0, self::COLUMNS - strlen($this->timeTakenReadable))) . $this->timeTakenReadable;
        $this->printable = implode("\n", $this->lines);
        $this->printable .= "\r\n";

        return $this;
    }

    public function __toString(): string
    {
        return $this->printable;
    }

    private function handleCallables(): void
    {
        /**
         * @var int $pos
         * @var string $name
         */
        foreach ($this->benchmark->index() as $pos => $name) {
            if ($this->isAborted) {
                $this->timeTaken = $this->timeTaken ?? (int) hrtime(true) - $this->startupTime;

                break;
            }
            $timeInit = (int) hrtime(true);
            $this->runs = 0;
            $this->runCallableAt($pos);
            $timeFinish = (int) hrtime(true);
            $timeTook = (int) ($timeFinish - $timeInit);
            $this->records->put($name, $timeTook);
            $this->results->put($name, [
                'time' => $timeTook,
                'runs' => $this->runs,
            ]);
            $this->timeTaken += $timeTook;
        }
    }

    private function runCallableAt(int $pos): void
    {
        $callable = $this->benchmark->callables()->get($pos);
        $name = $this->benchmark->index()->get($pos);
        for ($i = 0; $i < $this->times; ++$i) {
            $this->isPHPAborted = !$this->canPHPKeepGoing();
            $this->isSelfAborted = !$this->canSelfKeepGoing();
            if ($this->isPHPAborted || $this->isSelfAborted) {
                $this->isAborted = true;

                break;
            }

            try {
                $callable(...$this->benchmark->arguments());
            } catch (\ArgumentCountError $e) {
                throw new ArgumentCountError(
                    previous: $e,
                    message: $this->getErrorMessage($name),
                );
            } catch (\TypeError $e) {
                throw new TypeError(
                    previous: $e,
                    message: $this->getErrorMessage($name),
                );
            }
            ++$this->runs;
        }
    }

    private function getErrorMessage(string $name): MessageInterface
    {
        return (new Message('Error running callable %name%'))
            ->code('%name%', $name);
    }

    private function processCallableStats(): void
    {
        $this->records->sort();
        $this->recordsCount = count($this->records);
        $fastestTime = $this->records->first()->value;
        if ($this->recordsCount > 1) {
            /**
             * @var string $name
             * @var int $timeTaken
             */
            foreach ($this->records->getIterator() as $name => $timeTaken) {
                /** @var array $resultsAdd */
                $resultsAdd = $this->results->get($name);
                $resultsAdd['adds'] = number_format(100 * ($timeTaken - $fastestTime) / $fastestTime, 2) . '%';
                $this->results->put($name, $resultsAdd);
            }
        }
    }

    private function processResults(): void
    {
        /**
         * @var string $name
         * @var int $time
         */
        foreach ($this->records as $name => $time) {
            $this->lines[] = $this->getResultTitle($name);
            $number = $this->results->get($name)['runs'];
            $resRuns = $number . ' runs';
            $resRuns .= ' in ' . (new HrTime($time))->toReadMs();
            if ($number !== $this->times) {
                $resRuns .= ' ~ missed ' . ($this->times - $this->results->get($name)['runs']) . ' runs';
            }
            $this->lines[] = $resRuns;
            $this->lines[] = $this->lineSeparator;
            ++$this->recordsProcessed;
        }
    }

    private function handleAborted(): void
    {
        if ($this->isAborted) {
            $this->lines[] = 'Note: Process aborted (' . ($this->isPHPAborted ? 'PHP' : 'self') . ' time limit)';
            $this->lines[] = $this->lineSeparator;
        }
    }

    private function getResultTitle(string $name): string
    {
        $resultTitle = $name;
        if ($this->recordsProcessed === 0) {
            if ($this->recordsCount > 0) {
                $resultTitle .= ' (fastest)';
            }
        } else {
            $resultTitle .= ' (' . $this->results->get($name)['adds'] . ' slower)';
        }

        return $resultTitle;
    }

    private function canSelfKeepGoing(): bool
    {
        if ($this->timeLimit !== 0 && (int) hrtime(true) - $this->constructTime > $this->timeLimit) {
            return false;
        }

        return true;
    }

    private function canPHPKeepGoing(): bool
    {
        if ($this->maxExecutionTime !== 0 && time() - $this->requestTime > $this->maxExecutionTime) {
            return false;
        }

        return true;
    }
}
