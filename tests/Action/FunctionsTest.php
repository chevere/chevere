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

use function Chevere\Action\actionRun;
use Chevere\Action\Interfaces\ActionRunInterface;
use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Tests\Action\_resources\src\ActionRunnerTestController;
use Chevere\Tests\Action\_resources\src\ActionRunnerTestControllerRunFail;
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    public function testActionRunFailure(): void
    {
        $controller = new ActionRunnerTestControllerRunFail();
        $ran = $this->getFailedRan($controller);
        $this->assertSame(1, $ran->code());
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame(
            'Something went wrong',
            $ran->throwable()->getMessage()
        );
    }

    public function testActionRunWithArguments(): void
    {
        $parameter = 'name';
        $value = 'PeoplesHernandez';
        $action = new ActionRunnerTestController();
        $arguments = [
            $parameter => $value,
        ];
        $run = actionRun($action, ...$arguments);
        $this->assertSame(0, $run->code());
        $this->assertSame([
            'user' => $value,
        ], $run->data());
    }

    private function getFailedRan(ControllerInterface $controller): ActionRunInterface
    {
        return actionRun($controller, ...[]);
    }
}
