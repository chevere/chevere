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

use ArgumentCountError;
use LogicException;
use TypeError;
use DateTime;
use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;
use Chevere\Components\Benchmark\Exceptions\ArgumentTypeException;
use Chevere\Components\Benchmark\Exceptions\NoCallablesException;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Chevere\Components\Message\Message;
use Chevere\Components\Number\Number;
use Chevere\Components\Time\TimeHr;
use Chevere\Contracts\Benchmark\BenchmarkContract;

/**
 * Benchmark provides a way to profile callables execution.
 */
final class Benchmark implements BenchmarkContract
{
    /** @var int Determines the number of colums used for output. */
    const COLUMNS = 50;

    /** @var string Printable string */
    private string $printable;

    /** @var int Nanotime construct object */
    private int $constructTime;

    /** @var int time */
    private int $maxExecutionTime;

    /** @var float time */
    private float $requestTime;

    /** @var int Number of times to run each callable */
    private int $times;

    private ConsoleColor $consoleColor;

    /** @var int Maximum time allowed for the benchmark, in seconds */
    private int $timeLimit;

    /** @var array Arguments that will be passed to callables */
    private array $arguments;

    /** @var array [id => $callableName] */
    private array $index;

    /** @var array [id => $callable] */
    private array $callables;

    /** @var array [id => $timeTaken] The time taken by each callable */
    private array $records;

    /** @var array The results (readable) for each callable */
    private array $results;

    /** @var bool True if isPHPAborted || isSelfAborted */
    private bool $isAborted;

    /** @var bool True if PHP execution time is about to run out */
    private bool $isPHPAborted;

    /** @var bool True if the timeLimit has been reached */
    private bool $isSelfAborted;

    /** @var int Nanotime just before running the callables */
    private int $startupTime;

    /** @var int Time taken to run the benchmark */
    private int $timeTaken;

    /** @var int */
    private int $recordsCount;

    /** @var int */
    private int $recordsProcessed;

    /** @var array The benchmark document (lines) */
    private array $lines;

    /** @var int Auxiliar variable used to store the number of times each callable runs */
    private int $runs;

    /** @var string Line separator fixed to column width */
    private string $lineSeparator;

    /** @var string The readable string, like `Time taken: 10s` */
    private string $timeTakenReadable;

    /**
     * {@inheritdoc}
     */
    public function __construct(int $times)
    {
        $this->constructTime = (int) hrtime(true);
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->requestTime = BootstrapInstance::get()->time();
        $this->times = $times;
        if (BootstrapInstance::get()->cli()) {
            $this->consoleColor = new ConsoleColor();
        }
        $this->timeLimit = $this->maxExecutionTime;
        $this->arguments = [];
        $this->index = [];
        $this->callables = [];
        $this->timeTaken = 0;
        $this->printable = '';
    }

    /**
     * {@inheritdoc}
     */
    public function withTimeLimit(int $timeLimit): BenchmarkContract
    {
        $new = clone $this;
        $new->timeLimit = $timeLimit;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function timeLimit(): int
    {
        return $this->timeLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function withArguments(...$arguments): BenchmarkContract
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedCallable(callable $callable, string $name): BenchmarkContract
    {
        $new = clone $this;
        if (isset($new->index) && in_array($name, $new->index)) {
            throw new LogicException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $name)
                    ->toString()
            );
        }
        $new->index[] = $name;
        $new->callables[] = $callable;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function callables(): array
    {
        return $this->callables;
    }

    /**
     * {@inheritdoc}
     */
    public function index(): array
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function exec(): BenchmarkContract
    {
        $this->assertIndex();
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
        if (isset($this->consoleColor)) {
            $this->lineSeparator = $this->consoleColor->apply('blue', $this->lineSeparator);
            $pipe = $this->consoleColor->apply('blue', $pipe);
        }
        $pad = (int) round((static::COLUMNS - (strlen($title) + 1)) / 2, 0);
        $head = $pipe . str_repeat(' ', $pad) . $title . str_repeat(' ', floor($pad) == $pad ? ($pad - 1) : $pad) . $pipe;
        $this->lines = [
            $this->lineSeparator,
            $head,
            $this->lineSeparator,
            'Start: ' . gmdate(DateTime::ATOM),
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
        if (isset($this->consoleColor)) {
            $this->printable .= "\r\n";
        } else {
            $this->printable = '<pre>' . $this->printable . '</pre>' . "\t\n";
        }

        return $this;
    }

    public function toString(): string
    {
        return $this->printable;
    }

    private function assertIndex(): void
    {
        if (empty($this->index)) {
            throw new NoCallablesException(
                (new Message('No callables to benchmark, declare callables using the %method% method'))
                    ->code('%method%', __CLASS__ . '::withAddedCallable')
                    ->toString()
            );
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
            $timeTaken = intval($timeFinish - $timeInit);
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
        $key = array_search($callable, $this->callables);
        $name = $this->index[$key];
        for ($i = 0; $i < $this->times; ++$i) {
            $this->isPHPAborted = !$this->canPHPKeepGoing();
            $this->isSelfAborted = !$this->canSelfKeepGoing();
            if ($this->isPHPAborted || $this->isSelfAborted) {
                $this->isAborted = true;
                break;
            }
            try {
                $callable(...$this->arguments);
            } catch (ArgumentCountError $e) {
                throw new ArgumentCountException(
                    $this->getArgumentCountMessage($name, $e->getMessage())
                );
            } catch (TypeError $e) {
                throw new ArgumentTypeException(
                    $this->getTypeErrorMessage($name, $e->getMessage())
                );
            }
            ++$this->runs;
        }
    }

    private function getArgumentCountMessage(string $name, string $message): string
    {
        $pattern = '#^(.+\(\)) (.*)$#';
        preg_match($pattern, $message, $matches);
        $callable = $matches[1];
        $message = preg_replace($pattern, 'Callable %callable% named %name% $2', $message);

        return
            (new Message($message))
                ->code('%callable%', $callable)
                ->code('%name%', $name)
                ->toString();
    }

    private function getTypeErrorMessage(string $name, string $message): string
    {
        $pattern = '#^(Argument )(\d+)( passed to )(.*)( must .* type )(.*)(, )(.*)( given.*)$#';
        preg_match($pattern, $message, $matches);
        $argumentId = $matches[2];
        $callable = $matches[4];
        $typeExpected = $matches[6];
        $typeProvided = $matches[8];
        $message = preg_replace($pattern, '$1%argumentId%$3%callable% named %name%$5%typeExpected%$7%typeProvided%$9', $message);

        return
            (new Message($message))
                ->code('%argumentId%', $argumentId)
                ->code('%callable%', $callable)
                ->code('%name%', $name)
                ->code('%typeExpected%', $typeExpected)
                ->code('%typeProvided%', $typeProvided)
                ->toString();
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
        if (isset($this->consoleColor)) {
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
