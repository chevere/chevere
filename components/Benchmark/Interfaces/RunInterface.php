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

namespace Chevere\Components\Benchmark\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface RunInterface extends ToStringInterface
{
    /** @var int Determines the number of colums used for output. */
    const COLUMNS = 50;

    public function __construct(RunableInterface $runable);

    /**
     * @param int $times Number of times this benchmark should run
     */
    public function withTimes(int $times): RunInterface;

    /**
     *
     * @return int Number of times to run
     */
    public function times(): int;

    /**
     * @param int $timeLimit time limit for the benchmark, in seconds
     */
    public function withTimeLimit(int $timeLimit): RunInterface;

    public function timeLimit(): int;

    public function exec();

    /**
     * @return string Formated result summary
     */
    public function toString(): string;
}
