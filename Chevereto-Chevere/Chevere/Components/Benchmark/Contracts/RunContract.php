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

namespace Chevere\Components\Benchmark\Contracts;

use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;
use Chevere\Components\Common\Contracts\ToStringContract;

interface RunContract extends ToStringContract
{
    /** @var int Determines the number of colums used for output. */
    const COLUMNS = 50;

    public function __construct(RunableContract $runable);

    /**
     * @param int $times Number of times this benchmark should run
     */
    public function withTimes(int $times): RunContract;

    /**
     *
     * @return int Number of times to run
     */
    public function times(): int;

    /**
     * @param int $timeLimit time limit for the benchmark, in seconds
     */
    public function withTimeLimit(int $timeLimit): RunContract;

    public function timeLimit(): int;

    /**
     * @return string Formated result summary
     */
    public function toString(): string;
}
