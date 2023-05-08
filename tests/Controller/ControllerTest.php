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

use Chevere\Tests\Controller\_resources\ControllerTestController;
use Chevere\Tests\Controller\_resources\ControllerTestInvalidController;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ControllerTest extends TestCase
{
    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ControllerTestInvalidController())->assert();
    }

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        (new ControllerTestController())->assert();
    }
}
