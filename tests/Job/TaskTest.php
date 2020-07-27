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
        $name = 'task-name';
        $callable = 'callable';
        $arguments = [];
        $task = new Task($name, $callable, $arguments);
        $this->assertSame($name, $task->name());
        $this->assertSame($callable, $task->callable());
        $this->assertSame($arguments, $task->arguments());
    }
}
