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

use Chevere\Components\Benchmark\Exceptions\NoCallablesException;

interface RunableContract
{
    /**
     * @throws NoCallablesException if $benchmark doesn't declare any callable
     */
    public function __construct(BenchmarkContract $benchmark);

    /**
     * @return BenchmarkContract A runable BenchmarkContract
     */
    public function benchmark(): BenchmarkContract;
}
