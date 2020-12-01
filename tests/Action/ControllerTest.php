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

use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Tests\Action\_resources\src\ControllerTestController;
use Chevere\Tests\Action\_resources\src\ControllerTestInvalidController;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testConstructInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerTestInvalidController;
    }

    public function testConstruct(): void
    {
        $controller = new ControllerTestController;
        $this->assertSame(Type::STRING, $controller->getParametersTypeName());
        $newController = $controller->withSetUp();
        $this->assertEquals($controller, $newController);
    }
}
