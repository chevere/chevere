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
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Task('callable');
    }

    public function testUnexpectedArgument(): void
    {
        $this->expectException(UnexpectedValueException::class);
        new Task(__NAMESPACE__ . '\taskTestInvalidReturnType');
    }

    public function testArgumentCountError(): void
    {
        $this->expectException(ArgumentCountException::class);
        (new Task(__NAMESPACE__ . '\taskTestStep0'))
            ->withArguments('foo', 'invalid extra argument');
    }

    public function testConstruct(): void
    {
        $action = __NAMESPACE__ . '\taskTestStep';
        $task = new Task($action);
        $this->assertSame($action, $task->action());
        $this->assertSame([], $task->arguments());
        $arguments = ['1', '2', '3'];
        $task = $task->withArguments(...$arguments);
        $this->assertSame($arguments, $task->arguments());
    }
}

function taskTestStep(string $one, string $two, string $three): ResponseInterface
{
    return new ResponseSuccess([]);
}

function taskTestInvalidReturnType(): int
{
    return 1;
}

function taskTestStep0(): ResponseInterface
{
    return new ResponseSuccess([]);
}
