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

namespace Chevere\Tests\Controller;

use Chevere\Components\Controller\ControllerRunner;
use Chevere\Interfaces\Controller\ControllerExecutedInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Tests\Controller\_resources\src\ControllerRunnerTestController;
use Chevere\Tests\Controller\_resources\src\ControllerRunnerTestControllerRunFail;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    private function getFailedRan(ControllerInterface $controller): ControllerExecutedInterface
    {
        return (new ControllerRunner($controller))->execute([]);
    }

    public function testControllerRunFailure(): void
    {
        $controller = new ControllerRunnerTestControllerRunFail;
        $ran = $this->getFailedRan($controller);
        $this->assertSame(1, $ran->code());
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame('Something went wrong', $ran->throwable()->getMessage());
    }

    public function testRunWithArguments(): void
    {
        $parameter = 'name';
        $value = 'PeoplesHernandez';
        $controller = new ControllerRunnerTestController;
        $arguments = [$parameter => $value];
        $execute = (new ControllerRunner($controller))->execute($arguments);
        $this->assertSame(0, $execute->code());
        $this->assertSame(['user' => $value], $execute->data());
    }
}
