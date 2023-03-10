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
use Chevere\Controller\Exceptions\ControllerNameInterfaceException;
use Chevere\Controller\Exceptions\ControllerNameNotExistsException;
use Chevere\Tests\Controller\_resources\ControllerNameTestController;
use PHPUnit\Framework\TestCase;

final class ControllerNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(ControllerNameNotExistsException::class);
        new ControllerName('');
    }

    public function testCtypeSpace(): void
    {
        $this->expectException(ControllerNameNotExistsException::class);
        new ControllerName(' ');
    }

    public function testContainSpaces(): void
    {
        $this->expectException(ControllerNameNotExistsException::class);
        new ControllerName('a name');
    }

    public function testNotExists(): void
    {
        $this->expectException(ControllerNameNotExistsException::class);
        new ControllerName('not-found');
    }

    public function testWrongInterface(): void
    {
        $this->expectException(ControllerNameInterfaceException::class);
        new ControllerName(self::class);
    }

    public function testConstruct(): void
    {
        $className = ControllerNameTestController::class;
        $controllerName = new ControllerName($className);
        $this->assertSame($className, $controllerName->__toString());
    }
}
