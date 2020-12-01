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

use Chevere\Components\Action\ControllerName;
use Chevere\Exceptions\Controller\ControllerInterfaceException;
use Chevere\Exceptions\Controller\ControllerNameException;
use Chevere\Exceptions\Controller\ControllerNotExistsException;
use Chevere\Tests\Action\_resources\src\ControllerNameTestController;
use PHPUnit\Framework\TestCase;

final class ControllerNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(ControllerNameException::class);
        new ControllerName('');
    }

    public function testCtypeSpace(): void
    {
        $this->expectException(ControllerNameException::class);
        new ControllerName(' ');
    }

    public function testContainSpaces(): void
    {
        $this->expectException(ControllerNameException::class);
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
        $className = ControllerNameTestController::class;
        $controllerName = new ControllerName($className);
        $this->assertSame($className, $controllerName->toString());
    }
}
