<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\ControllerRunner;
use Chevere\Components\App\Exceptions\ControllerContractException;
use Chevere\Components\App\Exceptions\ControllerNotExistsException;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Response;
use Chevere\Contracts\App\ControllerRunnerContract;
use Chevere\TestApp\App\Controllers\Test;
use PHPUnit\Framework\TestCase;

final class ControllerRunnerTest extends TestCase
{
    /** @var ControllerRunnerContract */
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

    public function testRunnerWithInvalidControllerContract(): void
    {
        $this->expectException(ControllerContractException::class);
        $this->instance->run(ControllerRunner::class);
    }

    public function testRunner(): void
    {
        $controller = $this->instance->run(Test::class);
        $this->assertSame('Test', $controller->content());
    }
}