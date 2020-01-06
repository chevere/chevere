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

use Chevere\Components\Benchmark\Exceptions\NoCallablesException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Benchmark\BenchmarkContract;
use Chevere\Contracts\Benchmark\RunableContract;

/**
 * Determine if a BenchmarkContract can run
 */
final class Runable implements RunableContract
{
    private BenchmarkContract $benchmark;

    public function __construct(BenchmarkContract $benchmark)
    {
        $this->benchmark = $benchmark;
        $this->assertIndex();
    }

    /**
     * @return BenchmarkContract A runable BenchmarkContract
     */
    public function benchmark(): BenchmarkContract
    {
        return $this->benchmark;
    }

    private function assertIndex(): void
    {
        if (empty($this->benchmark->index())) {
            $className = get_class($this->benchmark);
            throw new NoCallablesException(
                (new Message('No callables defined for object of class %className%, declare callables using the %method% method'))
                    ->code('%className%', $className)
                    ->code('%method%', $className . '::withAddedCallable')
                    ->toString()
            );
        }
    }
}
