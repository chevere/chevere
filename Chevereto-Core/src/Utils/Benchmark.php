<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Utils;

use Chevereto\Core\Message;
use Chevereto\Core\Traits\PrintableTrait;
use Exception;

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
    /**
     * Determines the number of colums used for output.
     */
    const COLUMNS = 50;
    protected $COLUMNS;

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

    public $times;
    public $timeLimit = null;
    public $results;

    /**
     * Construct the Benchmark object.
     *
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
        if ($timeLimit !== null) {
            $this->timeLimit = $timeLimit;
        }
        $this->COLUMNS = (int) static::COLUMNS;
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
            if ($this->unnammedCnt == null) {
                $this->unnammedCnt = '1';
            }
            $name = 'Unnammed#'.$this->unnammedCnt;
            ++$this->unnammedCnt;
        }
        if ($this->callables != null && array_key_exists($name, $this->callables)) {
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
        $results = [];
        $aux = 0;
        $isAborted = false;
        $isPHPAborted = false;
        $isSelfAborted = false;
        $benchmarkStartTime = microtime(true);
        foreach ($this->callables as $k => $v) {
            if ($isAborted) {
                $this->time = microtime(true) - $benchmarkStartTime;
                break;
            }
            $runs = 0;
            $timeInit = microtime(true);
            for ($i = 0; $i < $this->times; ++$i) {
                $isPHPAborted = !$this->canPHPKeepGoing();
                $isSelfAborted = !$this->canSelfKeepGoing();
                if ($isPHPAborted || $isSelfAborted) {
                    $isAborted = true;
                    break;
                }
                $v(...($this->arguments ?: []));
                ++$runs;
            }
            ++$aux;
            if ($aux == $this->totalCnt) {
                $this->time = microtime(true) - $benchmarkStartTime;
            }
            $timeFinish = microtime(true);
            $timeTaken = floatval($timeFinish - $timeInit);
            $this->index[$k] = $timeTaken;
            $results[$k] = [
              'time' => $timeTaken,
              'runs' => $runs,
            ];
        }
        asort($this->index);
        if (count($this->index) == 1) {
            $this->results = $results;
        } else {
            // Add the extra % taken... wow, such insight
            foreach ($this->index as $k => $v) {
                $timeTaken = $results[$k]['time'];
                if (!isset($fastestTime)) {
                    $fastestTime = $timeTaken;
                } else {
                    $results[$k]['adds'] = round(100 * (($timeTaken - $fastestTime) / $fastestTime)).'%';
                }
                $this->results[$k] = $results[$k];
            }
        }
        $title = __CLASS__.' results';
        $border = 1;
        $lineChar = '-';
        $line = str_repeat($lineChar, $this->COLUMNS);
        $pad = (int) round(($this->COLUMNS - (strlen($title) + $border)) / 2, 0);
        $head = '|'.str_repeat(' ', $pad).$title.str_repeat(' ', floor($pad) == $pad ? ($pad - 1) : $pad).'|';
        $return = [
            $line,
            $head,
            $line,
            'Start: '.DateTime::getUTC(),
            'Hostname: '.gethostname(),
            'PHP version: '.phpversion(),
            'Server: '.php_uname('s').' '.php_uname('r').' '.php_uname('m'),
            $line,
        ];
        $i = 1;
        foreach ($this->results as $k => $v) {
            $res = $k;
            if ($i == 1) {
                if (count($this->index) > 1) {
                    $res .= ' (fastest)';
                }
                ++$i;
            } else {
                $res .= ' ('.$v['adds'].' slower)';
            }
            $return[] = $res;
            $resRuns = Number::abbreviate($v['runs']).' runs';
            $resRuns .= ' in '.round($v['time'], 4).' s';
            if ($v['runs'] != $this->times) {
                $resRuns .= ' ~ missed '.($this->times - $v['runs']).' runs';
            }
            $return[] = $resRuns;
            $return[] = $line;
        }
        if ($isAborted) {
            $return[] = 'Note: Process aborted ('.($isPHPAborted ? 'PHP' : 'self').' time limit)';
            $return[] = $line;
        }
        $total = ' Time taken: '.round($this->time, 4).' s';
        // $cols = strlen($total);
        // if ($cols > $this->COLUMNS) {
        //     $times = Number::abbreviate($runs, 2);
        // }
        $return[] = str_repeat(' ', (int) max(0, $this->COLUMNS - strlen($total))).$total;
        $this->printable = '<pre>'.implode("\n", $return).'</pre>';
    }

    protected function canSelfKeepGoing(): bool
    {
        if ($this->timeLimit != null && microtime(true) - $this->constructTime > $this->timeLimit) {
            return false;
        }

        return true;
    }

    protected function canPHPKeepGoing(): bool
    {
        if ($this->maxExecutionTime != 0 && microtime(true) - $this->requestTime > $this->maxExecutionTime) {
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
