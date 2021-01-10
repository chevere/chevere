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

namespace Chevere\Components\Benchmark;

use ArgumentCountError;
use Chevere\Components\HrTime\HrTime;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Benchmark\BenchmarkInterface;
use Chevere\Interfaces\Benchmark\BenchmarkRunInterface;
use Chevere\Interfaces\Message\MessageInterface;
use DateTime;
use TypeError;

/**
 * @codeCoverageIgnore
 */
final class BenchmarkRun implements BenchmarkRunInterface
{
    private BenchmarkInterface $benchmark;

    private int $timeLimit;

    private int $maxExecutionTime;

    private int $constructTime;

    private int $requestTime = 0;

    private int $times;

    private array $records;

    private array $results;

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
     * @throws ArgumentCountException if the argument count doesn't match the callable parameters
     * @throws TypeException if the argument types doesn't match
     */
    public function __construct(BenchmarkInterface $benchmark)
    {
        if (empty($benchmark->index())) {
            throw new InvalidArgumentException(
                (new Message('No callables defined for object of class %className%, declare callables using the %method% method'))
                    ->code('%className%', $benchmark::class)
                    ->code('%method%', $benchmark::class . '::withAddedCallable')
            );
        }
        $this->benchmark = $benchmark;
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->timeLimit = $this->maxExecutionTime;
        $this->requestTime = hrtime(true);
        $this->constructTime = (int) $this->requestTime;
        $this->times = 1;
        $this->timeTaken = 0;
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
        $this->records = [];
        $this->results = [];
        $this->isAborted = false;
        $this->isPHPAborted = false;
        $this->isSelfAborted = false;
        $this->startupTime = (int) hrtime(true);
        $this->handleCallables();
        $this->processCallablesStats();
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

    public function toString(): string
    {
        return $this->printable;
    }

    private function handleCallables(): void
    {
        foreach (array_keys($this->benchmark->index()) as $id) {
            if ($this->isAborted) {
                $this->timeTaken = $this->timeTaken ?? (int) hrtime(true) - $this->startupTime;

                break;
            }
            $timeInit = (int) hrtime(true);
            $this->runs = 0;
            $this->runCallable($this->benchmark->callables()[$id]);
            $timeFinish = (int) hrtime(true);
            $timeTaken = intval($timeFinish - $timeInit);
            $this->records[$id] = $timeTaken;
            $this->results[$id] = [
                'time' => $timeTaken,
                'runs' => $this->runs,
            ];
            $this->timeTaken += $timeTaken;
        }
    }

    private function runCallable(callable $callable): void
    {
        $key = array_search($callable, $this->benchmark->callables(), true);
        $name = $this->benchmark->index()[$key];
        for ($i = 0; $i < $this->times; ++$i) {
            $this->isPHPAborted = ! $this->canPHPKeepGoing();
            $this->isSelfAborted = ! $this->canSelfKeepGoing();
            if ($this->isPHPAborted || $this->isSelfAborted) {
                $this->isAborted = true;

                break;
            }

            try {
                $callable(...$this->benchmark->arguments());
            } catch (ArgumentCountError $e) {
                throw new ArgumentCountException(
                    $this->getErrorMessage($name, $e->getMessage()
                ));
            } catch (TypeError $e) {
                throw new TypeException(
                    $this->getErrorMessage($name, $e->getMessage()
                ));
            }
            ++$this->runs;
        }
    }

    private function getErrorMessage(string $name, string $message): MessageInterface
    {
        return (new Message('[Callable named %name%] %message%'))
            ->code('%name%', $name)
            ->strtr('%message%', $message);
    }

    private function processCallablesStats(): void
    {
        asort($this->records);
        $this->recordsCount = count($this->records);
        if ($this->recordsCount > 1) {
            foreach ($this->records as $id => $timeTaken) {
                if (! isset($fastestTime)) {
                    $fastestTime = $timeTaken;
                } else {
                    $this->results[$id]['adds'] = number_format(100 * ($timeTaken - $fastestTime) / $fastestTime, 2) . '%';
                }
            }
        }
    }

    private function processResults(): void
    {
        $this->recordsProcessed = 0;
        foreach (array_keys($this->records) as $id) {
            $this->lines[] = $this->getResultTitle($id);
            $number = $this->results[$id]['runs'];
            $resRuns = $number . ' runs';
            $resRuns .= ' in ' . (new HrTime($this->results[$id]['time']))->toReadMs();
            if ($this->results[$id]['runs'] !== $this->times) {
                $resRuns .= ' ~ missed ' . ($this->times - $this->results[$id]['runs']) . ' runs';
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

    private function getResultTitle(int $id): string
    {
        $name = $this->benchmark->index()[$id];
        $resultTitle = $name;
        if ($this->recordsProcessed === 0) {
            if ($this->recordsCount > 0) {
                $resultTitle .= ' (fastest)';
            }
        } else {
            $resultTitle .= ' (' . $this->results[$id]['adds'] . ' slower)';
        }
        if (isset($this->consoleColor)) {
            $resultTitle = $this->consoleColor->apply($this->recordsProcessed === 0 ? 'green' : 'red', $resultTitle);
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