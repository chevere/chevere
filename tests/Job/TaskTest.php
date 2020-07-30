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

namespace Chevere\Tests\Job;

use Chevere\Components\Job\Task;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testConstruct(): void
    {
        $callable = 'callable';
        $task = new Task($callable);
        $this->assertSame($callable, $task->callable());
        $this->assertSame([], $task->arguments());
        $arguments = ['1', '2', '3'];
        $task = $task->withArguments(...$arguments);
        $this->assertSame($arguments, $task->arguments());
    }
}
