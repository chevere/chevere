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

namespace Chevere\Components\Benchmark\Tests;

use Chevere\Components\Benchmark\Benchmark;
use Chevere\Components\Benchmark\Exceptions\NoCallablesException;
use Chevere\Components\Benchmark\Runable;
use PHPUnit\Framework\TestCase;

final class RunableTest extends TestCase
{
    public function testBadConstruct(): void
    {
        $this->expectException(NoCallablesException::class);
        new Runable(new Benchmark());
    }
}
