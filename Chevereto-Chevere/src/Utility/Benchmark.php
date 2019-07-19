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

namespace Chevereto\Chevere\Utility;

use Chevereto\Chevere\Message;
use Chevereto\Chevere\Traits\PrintableTrait;
use Exception;

// TODO: Needs console output

/**
 * Benchmark provides a simple way to determine which code procedure perform faster compared to others.
 */
// $benchmark = (new Benchmark(1000, 30))
//     ->arguments(1, 3)
//     ->add(function (int $a, int $b) {
//         return $a + $b;
//     }, 'Sum')
//     ->add(function (int $a, int $b) {
//         return $a/$b;
//     }, 'Division')
//     ->add(function (int $a, int $b) {
//         return $a * $b;
//     }, 'Multiply');
// print $benchmark;
class Benchmark
{
    use PrintableTrait;
    /** @var int Determines the number of colums used for output. */
    const COLUMNS = 50;
    protected $columns;

    protected $callables;
    protected $arguments;
    protected $index;
    protected $unnammedCnt;
    protected $totalCnt;
    protected $time;
    protected $printable;

    protected $maxExecutionTime;
    protected $constructTime;
    protected $requestTime;

    public $lineSeparator;
    public $timeTakenReadable;
    public $times;
    public $timeLimit = null;
    /** @var array */
    public $results;

    /** @var array */
    private $res;

    /** @var int */
    private $runs;

    /** @var bool */
    private $isAborted;

    /** @var bool */
    private $isPHPAborted;

    /** @var bool */
    private $isSelfAborted;

    /** @var float */
    private $startTimestamp;

    /**
     * @param int $times     number of times to run each function
     * @param int $timeLimit time limit for this benchmark, in seconds
     */
    public function __construct(int $times, int $timeLimit = null)
    {
        $this->constructTime = microtime(true); // - - $this->requestTime
        $this->maxExecutionTime = ini_get('max_execution_time');
        $this->requestTime = $_SERVER['REQUEST_TIME_FLOAT'] ?: 0;
        $this->times = $times;
        $this->totalCnt = 0;
        if (null !== $timeLimit) {
            $this->timeLimit = $timeLimit;
        }
        $this->columns = (int) static::COLUMNS;
    }

    /**
     * Set the callable arguments.
     *
     * @return self
     */
    public function arguments(): self
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
    public function add($callable, string $name = null): self
    {
        if (!isset($name)) {
            if (null == $this->unnammedCnt) {
                $this->unnammedCnt = '1';
            }
            $name = 'Unnammed#'.$this->unnammedCnt;
            ++$this->unnammedCnt;
        }
        if (null != $this->callables && array_key_exists($name, $this->callables)) {
            throw new Exception(
                (new Message('Duplicate callable declaration %s'))->code('%s', $name)
            );
        }
        if (!is_callable($callable)) {
            throw new Exception('Invalid callable declaration (not a callable)');
        }
        ++$this->totalCnt;
        $this->callables[$name] = $callable;

        return $this;
    }

    /**
     * Run the benchmark.
     */
    public function exec(): void
    {
        $this->index = [];
        $this->results = [];
        $this->isAborted = false;
        $this->isPHPAborted = false;
        $this->isSelfAborted = false;
        $this->startTimestamp = microtime(true);
        $this->handleCallables();
        $this->processCallablesStats();
        $title = __CLASS__.' results';
        $border = 1;
        $lineChar = '-';
        $this->lineSeparator = str_repeat($lineChar, $this->columns);
        $pad = (int) round(($this->columns - (strlen($title) + $border)) / 2, 0);
        $head = '|'.str_repeat(' ', $pad).$title.str_repeat(' ', floor($pad) == $pad ? ($pad - 1) : $pad).'|';
        $this->res = [
            $this->lineSeparator,
            $head,
            $this->lineSeparator,
            'Start: '.DateTime::getUTC(),
            'Hostname: '.gethostname(),
            'PHP version: '.phpversion(),
            'Server: '.php_uname('s').' '.php_uname('r').' '.php_uname('m'),
            $this->lineSeparator,
        ];
        $this->processResults();
        $this->handleAbortedRes();
        $this->timeTakenReadable = ' Time taken: '.round($this->time, 4).' s';
        $this->res[] = str_repeat(' ', (int) max(0, $this->columns - strlen($this->timeTakenReadable))).$this->timeTakenReadable;
        $this->printable = '<pre>'.implode("\n", $this->res).'</pre>';
    }

    protected function handleCallables(): void
    {
        foreach ($this->callables as $k => $v) {
            if ($this->isAborted) {
                $this->time = microtime(true) - $this->startTimestamp;
                break;
            }
            $timeInit = microtime(true);
            $this->runCallable($v);
            $timeFinish = microtime(true);
            $timeTaken = floatval($timeFinish - $timeInit);
            $this->index[$k] = $timeTaken;
            $this->results[$k] = [
                'time' => $timeTaken,
                'runs' => $this->runs,
            ];
        }
    }

    protected function runCallable(callable $callable): void
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
        ++$i;
        if ($i == $this->totalCnt) {
            $this->time = microtime(true) - $this->startTimestamp;
        }
    }

    protected function processCallablesStats(): void
    {
        asort($this->index);
        if (count($this->index) > 1) {
            foreach ($this->index as $k => $v) {
                $timeTaken = $this->results[$k]['time'];
                if (!isset($fastestTime)) {
                    $fastestTime = $timeTaken;
                } else {
                    $this->results[$k]['adds'] = round(100 * (($timeTaken - $fastestTime) / $fastestTime)).'%';
                }
            }
        }
    }

    protected function processResults(): void
    {
        $i = 1;
        foreach ($this->results as $k => $v) {
            $res = $k;
            if (1 == $i) {
                if (count($this->index) > 1) {
                    $res .= ' (fastest)';
                }
                ++$i;
            } else {
                $res .= ' ('.$v['adds'].' slower)';
            }
            $this->res[] = $res;
            $resRuns = Number::abbreviate($v['runs']).' runs';
            $resRuns .= ' in '.round($v['time'], 4).' s';
            if ($v['runs'] != $this->times) {
                $resRuns .= ' ~ missed '.($this->times - $v['runs']).' runs';
            }
            $this->res[] = $resRuns;
            $this->res[] = $this->lineSeparator;
        }
    }

    protected function handleAbortedRes(): void
    {
        if ($this->isAborted) {
            $this->res[] = 'Note: Process aborted ('.($this->isPHPAborted ? 'PHP' : 'self').' time limit)';
            $this->res[] = $this->lineSeparator;
        }
    }

    protected function canSelfKeepGoing(): bool
    {
        if (null != $this->timeLimit && microtime(true) - $this->constructTime > $this->timeLimit) {
            return false;
        }

        return true;
    }

    protected function canPHPKeepGoing(): bool
    {
        if (0 != $this->maxExecutionTime && microtime(true) - $this->requestTime > $this->maxExecutionTime) {
            return false;
        }

        return true;
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
}
