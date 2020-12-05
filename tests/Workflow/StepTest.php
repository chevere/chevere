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

use Chevere\Components\Workflow\Step;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Tests\Workflow\_resources\src\TaskTestStep0Action;
use Chevere\Tests\Workflow\_resources\src\TaskTestStep1Action;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class StepTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Step('name', 'callable');
    }

    public function testUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        new Step('name', __CLASS__);
    }

    public function testArgumentCountError(): void
    {
        $this->expectException(ArgumentCountException::class);
        (new Step('name', TaskTestStep0Action::class))
            ->withArguments(['foo' => 'foo', 'bar' => 'invalid extra argument']);
    }

    public function testConstruct(): void
    {
        $action = TaskTestStep1Action::class;
        $task = new Step('name', $action);
        $this->assertSame($action, $task->action());
        $this->assertSame([], $task->arguments());
        $arguments = ['foo' => '1', 'bar' => 123];
        $task = $task->withArguments($arguments);
        $this->assertSame($arguments, $task->arguments());
    }
}
