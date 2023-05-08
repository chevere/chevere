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

use Chevere\Controller\ControllerName;
use Chevere\String\Exceptions\CtypeSpaceException;
use Chevere\String\Exceptions\EmptyException;
use Chevere\Tests\Controller\_resources\ControllerNameTestController;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ControllerNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(EmptyException::class);
        new ControllerName('');
    }

    public function testCtypeSpace(): void
    {
        $this->expectException(CtypeSpaceException::class);
        new ControllerName(' ');
    }

    public function testNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName('not-found');
    }

    public function testWrongInterface(): void
    {
        $this->expectException(TypeError::class);
        new ControllerName(self::class);
    }

    public function testConstruct(): void
    {
        $className = ControllerNameTestController::class;
        $controllerName = new ControllerName($className);
        $this->assertSame($className, $controllerName->__toString());
    }
}
