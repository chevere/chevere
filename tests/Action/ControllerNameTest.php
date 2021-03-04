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

use Chevere\Components\Controller\ControllerName;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Tests\Action\_resources\src\ControllerNameTestController;
use PHPUnit\Framework\TestCase;

final class ControllerNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName('');
    }

    public function testCtypeSpace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName(' ');
    }

    public function testContainSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName('a name');
    }

    public function testNotExistent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName('not-found');
    }

    public function testWrongInterface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerName(self::class);
    }

    public function testConstruct(): void
    {
        $className = ControllerNameTestController::class;
        $controllerName = new ControllerName($className);
        $this->assertSame($className, $controllerName->toString());
    }
}
