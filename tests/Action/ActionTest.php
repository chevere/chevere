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

use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameters;
use Chevere\Tests\Action\_resources\src\ActionTestAction;
use Chevere\Tests\Action\_resources\src\ActionTestEmptyAction;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $action = new ActionTestEmptyAction;
        $parameters = new Parameters;
        $this->assertEquals($parameters, $action->getParameters());
        $this->assertEquals($parameters, $action->parameters());
        $arguments = new Arguments($parameters, []);
        $this->assertEquals($arguments, $action->getArguments([]));
    }

    public function testConstruct(): void
    {
        $action = new ActionTestAction;
        $this->assertSame('test', $action->description());
        $this->assertCount(0, $action->parameters());
        $this->assertCount(1, $action->responseDataParameters());
        $arguments = new Arguments($action->parameters(), []);
        $action->run($arguments->toArray());
    }
}
