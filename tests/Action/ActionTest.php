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

namespace Chevere\Tests\Action;

use Chevere\Parameter\Arguments;
use Chevere\Tests\Action\_resources\src\ActionTestAction;
use Chevere\Tests\Action\_resources\src\ActionTestMissingRunAction;
use Chevere\Tests\Action\_resources\src\ActionTestParamDefaultAction;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new ActionTestAction();
        $this->assertSame('test', $action->description());
        $this->assertCount(0, $action->parameters());
        $this->assertCount(1, $action->responseParameters());
        $arguments = new Arguments($action->parameters());
        $action->run($arguments);
    }

    public function testActionMissingRun(): void
    {
        $this->expectException(LogicException::class);
        new ActionTestMissingRunAction();
    }

    public function testActionDefaultParam(): void
    {
        $action = new ActionTestParamDefaultAction();
        $parameter = $action->parameters()->get('default');
        $this->assertSame('default', $parameter->default());
    }
}
