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

use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Str\Exceptions\StrContainsException;
use Chevere\Components\Str\Exceptions\StrCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use PHPUnit\Framework\TestCase;

final class ControllerParameterTest extends TestCase
{
    public function testEmptyName(): void
    {
        $this->expectException(StrEmptyException::class);
        new ControllerParameter('', new Regex('/.*/'));
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(StrCtypeSpaceException::class);
        new ControllerParameter(' ', new Regex('/.*/'));
    }

    public function testSpaceInName(): void
    {
        $this->expectException(StrContainsException::class);
        new ControllerParameter('some name', new Regex('/.*/'));
    }

    public function testConstruct(): void
    {
        $name = 'id';
        $regex = new Regex('/^[0-9+]$/');
        $controllerParameter = new ControllerParameter('id', $regex);
        $this->assertSame($name, $controllerParameter->name());
        $this->assertSame($regex->toString(), $controllerParameter->regex());
    }
}
