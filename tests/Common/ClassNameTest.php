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

namespace Chevere\Tests\Common;

use Chevere\Common\ClassName;
use Chevere\String\Exceptions\EmptyException;
use Chevere\Tests\HttpController\_resources\TestHttpController;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ClassNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(EmptyException::class);
        new ClassName('');
    }

    public function testNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ClassName('notFound');
    }

    public function testInterface(): void
    {
        $this->expectException(TypeError::class);
        (new ClassName(__CLASS__))->assertInterface(ClassName::class);
    }

    public function testConstruct(): void
    {
        $controller = new ClassName(TestHttpController::class);
        $this->assertSame(TestHttpController::class, $controller->__toString());
    }
}
