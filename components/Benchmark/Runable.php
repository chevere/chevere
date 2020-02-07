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

use Chevere\Components\Benchmark\Exceptions\NoCallablesException;
use Chevere\Components\Message\Message;
use Chevere\Components\Benchmark\Interfaces\BenchmarkInterface;
use Chevere\Components\Benchmark\Interfaces\RunableInterface;

/**
 * Determine if a BenchmarkInterface can run
 */
final class Runable implements RunableInterface
{
    private BenchmarkInterface $benchmark;

    public function __construct(BenchmarkInterface $benchmark)
    {
        $this->benchmark = $benchmark;
        $this->assertIndex();
    }

    /**
     * @return BenchmarkInterface A runable BenchmarkInterface
     */
    public function benchmark(): BenchmarkInterface
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
