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

namespace Chevere\Utility;

use const Chevere\CLI;

use LogicException;
use Chevere\Message;
use Chevere\Traits\PrintableTrait;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Benchmark provides a simple way to determine which code procedure perform faster.
 */
// $benchmark = (new Benchmark(10000))
//     ->setArguments(500, 3000)
//     ->add(function (int $a, int $b) {
//         return $a + $b;
//     }, 'Sum')
//     ->add(function (int $a, int $b) {
//         return $a / $b;
//     }, 'Division')
//     ->add(function (int $a, int $b) {
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

    /** @var float Microtime construct object */
    private $constructTime;

    /** @var float */
    private $maxExecutionTime;

    /** @var float Microtime $_SERVER['REQUEST_TIME_FLOAT'] */
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

    /** @var float Microtime just before running the callables */
    private $startupTime;

    /** @var float Time taken to run the benchmark */
    private $timeTaken;

    /** @var int */
    private $recordsCount;

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
        $this->constructTime = microtime(true);
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->requestTime = $_SERVER['REQUEST_TIME_FLOAT'] ?: 0;
        $this->times = $times;
        $this->callablesCount = 0;
        $this->timeTaken = 0;
        if (CLI) {
            $this->consoleColor = new ConsoleColor();
        }
    }

    /**
     * @param int $timeLimit Time limit for the benchmark, in seconds.
     */
    public function setTimeLimit(int $timeLimit): self
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    /**
     * Set the callable arguments.
     *
     * @return self
     */
    public function setArguments(): self
    {
        $this->arguments = func_get_args();

        return $this;
    }

    /**
     * Add a callable to the benchmark queue.
     *
     * @param callable $callable callable
     * @param string   $name     callable name, or alias for your own reference
     *
     * @return self
     */
    public function add(callable $callable, string $name = null): self
    {
        if (!isset($name)) {
            $this->unnammedCallablesCount = $this->unnammedCallablesCount ?? 1;
            $name = 'Unnammed#' . (string) $this->unnammedCallablesCount;
            ++$this->unnammedCallablesCount;
        }
        if (isset($this->index) && in_array($name, $this->index)) {
            throw new LogicException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $name)
                    ->toString()
            );
        }
        $this->index[] = $name;
        $this->callables[$this->callablesCount] = $callable;
        ++$this->callablesCount;

        return $this;
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
        $this->startupTime = microtime(true);
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
            'Start: ' . DateTime::getUTC(),
            'Hostname: ' . gethostname(),
            'PHP version: ' . phpversion(),
            'Server: ' . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m'),
            $this->lineSeparator,
        ];
        $this->processResults();
        $this->handleAbortedRes();
        $this->timeTakenReadable = ' Time taken: ' . $this->microtimeToRead($this->timeTaken);
        $this->lines[] = str_repeat(' ', (int) max(0, static::COLUMNS - strlen($this->timeTakenReadable))) . $this->timeTakenReadable;
        $this->printable = implode("\n", $this->lines);
        if (CLI) {
            $this->printable .= "\r\n";
        } else {
            $this->printable = '<pre>' . $this->printable . '</pre>' . "\t\n";
        }
    }

    /**
     * Get bcrypt optimal cost.
     *
     * @param float $time seconds to use for this test
     * @param int   $cost cost to be used as starting value
     *
     * @return int optimal BCrypt cost
     */
    public static function bcryptCost(float $time = 0.1, int $cost = 9): int
    {
        do {
            ++$cost;
            $ti = microtime(true);
            password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
            $tf = microtime(true);
        } while (($tf - $ti) < $time);

        return $cost;
    }

    private function handleCallables(): void
    {
        foreach ($this->index as $id => $name) {
            if ($this->isAborted) {
                $this->timeTaken = $this->timeTaken ?? (microtime(true) - $this->startupTime);
                break;
            }
            $timeInit = microtime(true);
            $this->runs = 0;
            $this->runCallable($this->callables[$id]);
            $timeFinish = microtime(true);
            $timeTaken = floatval($timeFinish - $timeInit);
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
                // $timeTaken = $this->results[$id]['time'];
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
        $i = 0;
        foreach ($this->records as $id => $timeTaken) {
            ++$i;
            $name = $this->index[$id];
            $resultTitle = $name;
            $result = $this->results[$id];
            if (1 == $i) {
                if ($this->recordsCount > 1) {
                    $resultTitle .= ' (fastest)';
                }
            } else {
                $resultTitle .= ' (' . $result['adds'] . ' slower)';
            }
            if (CLI) {
                $resultTitle = $this->consoleColor->apply(1 == $i ? 'green' : 'red', $resultTitle);
            }
            $this->lines[] = $resultTitle;
            $resRuns = Number::abbreviate($result['runs']) . ' runs';
            $resRuns .= ' in ' . round(1000 * $result['time'], 4) . ' ms';
            if ($result['runs'] != $this->times) {
                $resRuns .= ' ~ missed ' . ($this->times - $result['runs']) . ' runs';
            }
            $this->lines[] = $resRuns;
            $this->lines[] = $this->lineSeparator;
        }
    }

    private function handleAbortedRes(): void
    {
        if ($this->isAborted) {
            $this->lines[] = 'Note: Process aborted (' . ($this->isPHPAborted ? 'PHP' : 'self') . ' time limit)';
            $this->lines[] = $this->lineSeparator;
        }
    }

    private function canSelfKeepGoing(): bool
    {
        if (null != $this->timeLimit && microtime(true) - $this->constructTime > $this->timeLimit) {
            return false;
        }

        return true;
    }

    private function canPHPKeepGoing(): bool
    {
        if (0 != $this->maxExecutionTime && microtime(true) - $this->requestTime > $this->maxExecutionTime) {
            return false;
        }

        return true;
    }

    private function microtimeToRead(float $microtime): string
    {
        return number_format($microtime * 1000, 2) . ' ms';
    }
}
