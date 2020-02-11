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
use TypeError;
use DateTime;
use Chevere\Components\Instances\BootstrapInstance;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Chevere\Components\Benchmark\Interfaces\RunInterface;
use Chevere\Components\Benchmark\Interfaces\RunableInterface;
use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;
use Chevere\Components\Benchmark\Exceptions\ArgumentTypeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Number\Number;
use Chevere\Components\Time\TimeHr;

/**
 * Runs a prepared Benchmark
 * @codeCoverageIgnore
 */
final class Run implements RunInterface
{
    private RunableInterface $runable;

    /** @var int Maximum time allowed for the benchmark, in seconds */
    private int $timeLimit;

    /** @var int time */
    private int $maxExecutionTime;

    /** @var int Nanotime construct object */
    private int $constructTime;

    /** @var float time */
    private float $requestTime;

    /** @var int Number of times to run each callable */
    private int $times;

    private ConsoleColor $consoleColor;

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

    /** @var string Human-readable result summary */
    private string $printable;

    /**
     * Creates a new instance.
     *
     * @throws ArgumentCountException if the argument count doesn't match the callable parameters
     * @throws ArgumentTypeException if the argument types doesn't match
     */
    public function __construct(RunableInterface $runable)
    {
        $this->runable = $runable;
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->timeLimit = $this->maxExecutionTime;
        $this->constructTime = (int) hrtime(true);
        $this->times = 1;
        $this->timeTaken = 0;
        if (BootstrapInstance::get()->isCli()) {
            $this->consoleColor = new ConsoleColor();
        }
        $this->requestTime = BootstrapInstance::get()->time();
        $this->printable = '';
    }

    public function withTimes(int $times): RunInterface
    {
        $new = clone $this;
        $new->times = $times;

        return $new;
    }

    public function times(): int
    {
        return $this->times;
    }

    public function withTimeLimit(int $timeLimit): RunInterface
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
        if (isset($this->consoleColor)) {
            $this->lineSeparator = $this->consoleColor->apply('blue', $this->lineSeparator);
            $pipe = $this->consoleColor->apply('blue', $pipe);
        }
        $pad = (int) round((self::COLUMNS - (strlen($title) + 1)) / 2, 0);
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
        $this->lines[] = str_repeat(' ', (int) max(0, self::COLUMNS - strlen($this->timeTakenReadable))) . $this->timeTakenReadable;
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

    private function handleCallables(): void
    {
        foreach (array_keys($this->runable->benchmark()->index()) as $id) {
            if ($this->isAborted) {
                $this->timeTaken = $this->timeTaken ?? ((int) hrtime(true) - $this->startupTime);
                break;
            }
            $timeInit = (int) hrtime(true);
            $this->runs = 0;
            $this->runCallable($this->runable->benchmark()->callables()[$id]);
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
        $key = array_search($callable, $this->runable->benchmark()->callables());
        $name = $this->runable->benchmark()->index()[$key];
        for ($i = 0; $i < $this->times; ++$i) {
            $this->isPHPAborted = !$this->canPHPKeepGoing();
            $this->isSelfAborted = !$this->canSelfKeepGoing();
            if ($this->isPHPAborted || $this->isSelfAborted) {
                $this->isAborted = true;
                break;
            }
            try {
                call_user_func($callable, ...$this->runable->benchmark()->arguments());
            } catch (ArgumentCountError $e) {
                throw new ArgumentCountException(
                    $this->getErrorMessage($name, $e->getMessage())
                );
            } catch (TypeError $e) {
                throw new ArgumentTypeException(
                    $this->getErrorMessage($name, $e->getMessage())
                );
            }
            ++$this->runs;
        }
    }

    private function getErrorMessage(string $name, string $message): string
    {
        return (new Message('[Callable named %name%] %message%'))
            ->code('%name%', $name)
            ->strtr('%message%', $message)
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
        $name = $this->runable->benchmark()->index()[$id];
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
