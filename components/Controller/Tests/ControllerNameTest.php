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

namespace Chevere\Components\Controller\Tests;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\Exceptions\ControllerInterfaceException;
use Chevere\Components\Controller\Exceptions\ControllerNotExistsException;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Str\Exceptions\StrContainsException;
use Chevere\Components\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use PHPUnit\Framework\TestCase;

final class ControllerNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(StrEmptyException::class);
        new ControllerName('');
    }

    public function testCtypeSpace(): void
    {
        $this->expectException(StrCtypeSpaceException::class);
        new ControllerName(' ');
    }

    public function testContainSpaces(): void
    {
        $this->expectException(StrContainsException::class);
        new ControllerName('a name');
    }

    public function testNotExistent(): void
    {
        $this->expectException(ControllerNotExistsException::class);
        new ControllerName('not-found');
    }

    public function testWrongInterface(): void
    {
        $this->expectException(ControllerInterfaceException::class);
        new ControllerName(__CLASS__);
    }

    public function testConstruct(): void
    {
        $className = TestController::class;
        $controllerName = new ControllerName($className);
        $this->assertSame($className, $controllerName->toString());
    }
}

final class TestController extends Controller
{
    public function run(ControllerArgumentsInterface $arguments): void
    {
    }
}
