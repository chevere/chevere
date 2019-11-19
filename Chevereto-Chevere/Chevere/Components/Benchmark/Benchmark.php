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

namespace Chevere\Components\Benchmark;

use LogicException;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Chevere\Components\DateTime\DateTime;
use Chevere\Components\Message\Message;
use Chevere\Components\Number\Number;
use Chevere\Components\Time\TimeHr;
use Chevere\Components\Traits\PrintableTrait;
use const Chevere\BOOTSTRAP_TIME;
use const Chevere\CLI;

/**
 * Benchmark provides a simple way to determine which code procedure perform faster.
 */
// $benchmark = (new Benchmark(10000))
//     ->withArguments(500, 3000)
//     ->withAddedCallable(function (int $a, int $b) {
//         return $a + $b;
//     }, 'Sum')
//     ->withAddedCallable(function (int $a, int $b) {
//         return $a / $b;
//     }, 'Division')
//     ->withAddedCallable(function (int $a, int $b) {
//         return $a * $b;
//     }, 'Multiply');
// print $benchmark;
final class Benchmark
{
    use PrintableTrait;

    /** @var int Determines the number of colums used for output. */
    const COLUMNS = 50;

    /** @var string Printable string (PrintableTrait) */
    private $printable;

    /** @var int Nanotime construct object */
    private $constructTime;

    /** @var int time */
    private $maxExecutionTime;

    /** @var float time */
    private $requestTime;

    /** @var int Number of times to run each callable */
    private $times;

    /** @var int Count of callables passed */
    private $callablesCount;

    /** @var ConsoleColor */
    private $consoleColor;

    /** @var int Count of unnamed callables passed */
    private $unnammedCallablesCount;

    /** @var int Maximum time allowed for the benchmark, in seconds */
    private $timeLimit;

    /** @var array Arguments that will be passed to callables */
    private $arguments;

    /** @var array [id => $callableName] */
    private $index;

    /** @var array [id => $callable] */
    private $callables;

    /** @var array [id => $timeTaken] The time taken by each callable */
    private $records;

    /** @var array The results (readable) for each callable */
    private $results;

    /** @var bool True if isPHPAborted || isSelfAborted */
    private $isAborted;

    /** @var bool True if PHP execution time is about to run out */
    private $isPHPAborted;

    /** @var bool True if the timeLimit has been reached */
    private $isSelfAborted;

    /** @var int Nanotime just before running the callables */
    private $startupTime;

    /** @var int Time taken to run the benchmark */
    private $timeTaken;

    /** @var int */
    private $recordsCount;

    /** @var int */
    private $recordsProcessed;

    /** @var array The benchmark document (lines) */
    private $lines;

    /** @var int Auxiliar variable used to store the number of times each callable runs */
    private $runs;

    /** @var string Line separator fixed to column width */
    private $lineSeparator;

    /** @var string The readable string, like `Time taken: 10s` */
    private $timeTakenReadable;

    /**
     * @param int $times Number of times to run each callable
     */
    public function __construct(int $times)
    {
        $this->constructTime = (int) hrtime(true);
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->requestTime = BOOTSTRAP_TIME;
        $this->times = $times;
        $this->callablesCount = 0;
        $this->timeTaken = 0;
        if (CLI) {
            $this->consoleColor = new ConsoleColor();
        }
    }

    /**
     * @param int $timeLimit time limit for the benchmark, in seconds
     */
    public function withTimeLimit(int $timeLimit): Benchmark
    {
        $new = clone $this;
        $new->timeLimit = $timeLimit;

        return $new;
    }

    /**
     * Set the callable arguments.
     *
     * @return self
     */
    public function withArguments(): Benchmark
    {
        $new = clone $this;
        $new->arguments = func_get_args();

        return $new;
    }

    /**
     * Add a callable to the benchmark queue.
     *
     * @param callable $callable callable
     * @param string   $name     callable name, or alias for your own reference
     *
     * @return self
     */
    public function withAddedCallable(callable $callable, string $name = null): Benchmark
    {
        $new = clone $this;
        if (!isset($name)) {
            $new->unnammedCallablesCount = $new->unnammedCallablesCount ?? 1;
            $name = 'Unnammed#' . (string) $new->unnammedCallablesCount;
            ++$new->unnammedCallablesCount;
        }
        if (isset($new->index) && in_array($name, $new->index)) {
            throw new LogicException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $name)
                    ->toString()
            );
        }
        $new->index[] = $name;
        $new->callables[$new->callablesCount] = $callable;
        ++$new->callablesCount;

        return $new;
    }

    /**
     * Run the benchmark.
     */
    public function exec(): void
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
        $this->lineSeparator = str_repeat('-', static::COLUMNS);
        $pipe = '|';
        if (CLI) {
            $this->lineSeparator = $this->consoleColor->apply('blue', $this->lineSeparator);
            $pipe = $this->consoleColor->apply('blue', $pipe);
        }
        $pad = (int) round((static::COLUMNS - (strlen($title) + 1)) / 2, 0);
        $head = $pipe . str_repeat(' ', $pad) . $title . str_repeat(' ', floor($pad) == $pad ? ($pad - 1) : $pad) . $pipe;
        $this->lines = [
            $this->lineSeparator,
            $head,
            $this->lineSeparator,
            'Start: ' . DateTime::getUtcAtom(),
            'Hostname: ' . gethostname(),
            'PHP version: ' . phpversion(),
            'Server: ' . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m'),
            $this->lineSeparator,
        ];
        $this->processResults();
        $this->handleAborted();
        $this->timeTakenReadable = ' Time taken: ' . (new TimeHr($this->timeTaken))->toReadMs();
        $this->lines[] = str_repeat(' ', (int) max(0, static::COLUMNS - strlen($this->timeTakenReadable))) . $this->timeTakenReadable;
        $this->printable = implode("\n", $this->lines);
        if (CLI) {
            $this->printable .= "\r\n";
        } else {
            $this->printable = '<pre>' . $this->printable . '</pre>' . "\t\n";
        }
    }

    private function handleCallables(): void
    {
        foreach (array_keys($this->index) as $id) {
            if ($this->isAborted) {
                $this->timeTaken = $this->timeTaken ?? ((int) hrtime(true) - $this->startupTime);
                break;
            }
            $timeInit = (int) hrtime(true);
            $this->runs = 0;
            $this->runCallable($this->callables[$id]);
            $timeFinish = (int) hrtime(true);
            $timeTaken = floatval($timeFinish - $timeInit);
            $this->records[$id] = $timeTaken;
            $this->results[$id] = [
                'time' => $timeTaken,
                'runs' => $this->runs,
                //'ads' => ,
            ];
            $this->timeTaken += $timeTaken;
        }
    }

    private function runCallable(callable $callable): void
    {
        for ($i = 0; $i < $this->times; ++$i) {
            $this->isPHPAborted = !$this->canPHPKeepGoing();
            $this->isSelfAborted = !$this->canSelfKeepGoing();
            if ($this->isPHPAborted || $this->isSelfAborted) {
                $this->isAborted = true;
                break;
            }
            $callable(...($this->arguments ?: []));
            ++$this->runs;
        }
    }

    private function processCallablesStats(): void
    {
        asort($this->records);
        $this->recordsCount = count($this->records);
        if ($this->recordsCount > 1) {
            foreach ($this->records as $id => $timeTaken) {
                if (!isset($fastestTime)) {
                    $fastestTime = $timeTaken;
                } else {
                    $this->results[$id]['adds'] = number_format(100 * (($timeTaken - $fastestTime) / $fastestTime), 2) . '%';
                }
            }
        }
    }

    private function processResults(): void
    {
        $this->recordsProcessed = 0;
        foreach (array_keys($this->records) as $id) {
            $this->lines[] = $this->getResultTitle($id);
            $number = new Number($this->results[$id]['runs']);
            $resRuns = $number->toAbbreviate() . ' runs';
            $resRuns .= ' in ' . (new TimeHr($this->results[$id]['time']))->toReadMs();
            if ($this->results[$id]['runs'] != $this->times) {
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
        $name = $this->index[$id];
        $resultTitle = $name;
        if (0 == $this->recordsProcessed) {
            if ($this->recordsCount > 0) {
                $resultTitle .= ' (fastest)';
            }
        } else {
            $resultTitle .= ' (' . $this->results[$id]['adds'] . ' slower)';
        }
        if (CLI) {
            $resultTitle = $this->consoleColor->apply(0 == $this->recordsProcessed ? 'green' : 'red', $resultTitle);
        }

        return $resultTitle;
    }

    private function canSelfKeepGoing(): bool
    {
        if (null != $this->timeLimit && (int) hrtime(true) - $this->constructTime > $this->timeLimit) {
            return false;
        }

        return true;
    }

    private function canPHPKeepGoing(): bool
    {
        if (0 != $this->maxExecutionTime && time() - $this->requestTime > $this->maxExecutionTime) {
            return false;
        }

        return true;
    }
}
