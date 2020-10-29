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

use Chevere\Components\Action\Action;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Components\Workflow\Task;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class TaskTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Task('callable');
    }

    public function testUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        new Task(__CLASS__);
    }

    public function testArgumentCountError(): void
    {
        $this->expectException(ArgumentCountException::class);
        (new Task(TaskTestStep0::class))
            ->withArguments(['foo' => 'foo', 'bar' => 'invalid extra argument']);
    }

    public function testConstruct(): void
    {
        $action = TaskTestStep1::class;
        $task = new Task($action);
        $this->assertSame($action, $task->action());
        $this->assertSame([], $task->arguments());
        $arguments = ['foo' => '1'];
        $task = $task->withArguments($arguments);
        $this->assertSame($arguments, $task->arguments());
    }
}

class TaskTestStep0 extends Action
{
    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}

class TaskTestStep1 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('foo'));
    }

    public function run(ArgumentsInterface $arguments): ResponseInterface
    {
        return new ResponseSuccess([]);
    }
}
