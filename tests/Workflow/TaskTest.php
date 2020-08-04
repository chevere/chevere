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

namespace Chevere\Tests\Workflow;

use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Workflow\Task;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\UnexpectedValueException;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\ActionInterface;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Task('callable');
    }

    public function testArgumentCountError(): void
    {
        $this->expectException(ArgumentCountException::class);
        (new Task(TaskTestStep0::class))
            ->withArguments('foo', 'invalid extra argument');
    }

    public function testConstruct(): void
    {
        $action = TaskTestStep1::class;
        $task = new Task($action);
        $this->assertSame($action, $task->action());
        $this->assertSame([], $task->arguments());
        $arguments = ['1'];
        $task = $task->withArguments(...$arguments);
        $this->assertSame($arguments, $task->arguments());
    }
}

class TaskTestStep0 implements ActionInterface
{
    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}

class TaskTestStep1 implements ActionInterface
{
    public function __construct(string $one)
    {
    }

    public function execute(): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
