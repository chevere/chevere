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

use Chevere\Components\Controller\ControllerArgumentsMaker;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexMatchException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentsRequiredException;
use Chevere\Components\Regex\Regex;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ControllerArgumentsMakerTest extends TestCase
{
    public function testWithBindInvalidParameterName(): void
    {
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );

        $this->expectException(OutOfBoundsException::class);
        (new ControllerArgumentsMaker($parameters))
            ->withBind('name', 'value');
    }

    public function testWithBindInvalidRegexArgument(): void
    {
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );
        $this->expectException(ControllerArgumentRegexMatchException::class);
        (new ControllerArgumentsMaker($parameters))
            ->withBind('id', 'abc');
    }

    public function testWithBind(): void
    {
        $name = 'id';
        $value = '123';
        $maker = new ControllerArgumentsMaker(
            (new ControllerParameters)
                ->withParameter(
                    new ControllerParameter($name, new Regex('/^[0-9]+$/'))
                )
        );
        $maker = $maker->withBind($name, $value);
        $this->assertTrue($maker->arguments()->has($name));
        $this->assertSame($value, $maker->arguments()->get($name));
    }

    public function testArgumentsRequiredException(): void
    {
        $maker = new ControllerArgumentsMaker(
            (new ControllerParameters)
                ->withParameter(
                    new ControllerParameter('id', new Regex('/^[0-9]+$/'))
                )
        );
        $this->expectException(ControllerArgumentsRequiredException::class);
        $maker->arguments();
    }

    public function testWithBindOptionalArgument(): void
    {
        $paramId = 'id';
        $paramName = 'name';
        $maker = new ControllerArgumentsMaker(
            (new ControllerParameters)
                ->withParameter(
                    new ControllerParameter($paramId, new Regex('/^[0-9]+$/'))
                )
                ->withParameter(
                    (new ControllerParameter($paramName, new Regex('/^\w+$/')))
                        ->withIsRequired(false)
                )
        );
        $maker = $maker->withBind($paramId, '123');
        $maker->arguments();
        $this->assertFalse($maker->arguments()->has($paramName));
    }
}
