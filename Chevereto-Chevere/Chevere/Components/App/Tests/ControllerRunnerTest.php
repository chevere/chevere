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

namespace Chevere\Components\App\Tests;

use Chevere\Components\App\App;
use Chevere\Components\App\ControllerRunner;
use Chevere\Components\App\Exceptions\ControllerInterfaceException;
use Chevere\Components\App\Exceptions\ControllerNotExistsException;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Response;
use Chevere\Components\App\Interfaces\ControllerRunnerInterface;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    /** @var ControllerRunnerInterface */
    private $instance;

    protected function setUp(): void
    {
        $this->instance = new ControllerRunner(new App(new Services(), new Response()));
    }

    public function testRunnerWithNonexistentControllerName(): void
    {
        $this->expectException(ControllerNotExistsException::class);
        $this->instance->run('/');
    }

    public function testRunnerWithInvalidControllerInterface(): void
    {
        $this->expectException(ControllerInterfaceException::class);
        $this->instance->run(ControllerRunner::class);
    }

    public function testRunner(): void
    {
        $controller = $this->instance->run(TestController::class);
        $this->assertSame('Test', $controller->content());
    }
}
