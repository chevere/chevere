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

namespace Chevere\Benchmark\Interfaces;

use Stringable;

/**
 * Describes the component in charge of running a Benchmark.
 */
interface BenchmarkRunInterface extends Stringable
{
    /**
     * @var int Determines the number of columns used for output.
     */
    public const COLUMNS = 50;

    public function __construct(BenchmarkInterface $benchmark);

    /**
     * @param int $times Number of times this benchmark should run
     */
    public function withTimes(int $times): self;

    /**
     * @return int Number of times to run
     */
    public function times(): int;

    /**
     * @param int $timeLimit time limit for the benchmark, in seconds
     */
    public function withTimeLimit(int $timeLimit): self;

    public function timeLimit(): int;

    public function exec();

    /**
     * @return string Formated result summary
     */
    public function __toString(): string;
}
