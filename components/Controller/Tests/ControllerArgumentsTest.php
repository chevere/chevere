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

use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexMatchException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentsRequiredException;
use Chevere\Components\Regex\Regex;
use Ds\Map;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ControllerArgumentsTest extends TestCase
{
    public function testConstruct(): void
    {
        $array = [
            'id' => '1',
            'name' => 'someValue',
        ];
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^\d+$/'))
            )
            ->withParameter(
                new ControllerParameter('name', new Regex('/^\w+$/'))
            );
        $arguments = new Map($array);
        $controllerArguments = new ControllerArguments($parameters, $arguments);
        foreach ($array as $name => $value) {
            $this->assertTrue($controllerArguments->has($name));
            $this->assertSame($value, $controllerArguments->get($name));
        }
        $notFoundKey = '404';
        $this->assertFalse($controllerArguments->has($notFoundKey));
        $this->expectException(OutOfBoundsException::class);
        $controllerArguments->get($notFoundKey);
    }

    public function testInvalidParameterName(): void
    {
        $parameters = new ControllerParameters;
        $this->expectException(OutOfBoundsException::class);
        new ControllerArguments($parameters, new Map(['id' => '123']));
    }

    public function testInvalidRegexArgument(): void
    {
        $parameters = (new ControllerParameters)
            ->withParameter(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            );
        $this->expectException(ControllerArgumentRegexMatchException::class);
        (new ControllerArguments($parameters, new Map(['id' => 'abc'])));
    }

    public function testPut(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $controllerArguments = new ControllerArguments(
            (new ControllerParameters)
                ->withParameter(
                    new ControllerParameter($name, new Regex('/^[0-9]+$/'))
                ),
            new Map([$name => $value])
        );
        $this->assertTrue($controllerArguments->has($name));
        $this->assertSame($value, $controllerArguments->get($name));
        $controllerArguments->put($name, $valueAlt);
        $this->assertSame($valueAlt, $controllerArguments->get($name));
        $this->expectException(ControllerArgumentRegexMatchException::class);
        $controllerArguments->put($name, 'invalid');
    }

    public function testArgumentsRequiredException(): void
    {
        $this->expectException(ControllerArgumentsRequiredException::class);
        new ControllerArguments(
            (new ControllerParameters)
                ->withParameter(
                    new ControllerParameter('id', new Regex('/^[0-9]+$/'))
                ),
            new Map
        );
    }

    public function testOptionalArgument(): void
    {
        $paramId = 'id';
        $paramName = 'name';
        $controllerArguments = new ControllerArguments(
            (new ControllerParameters)
                ->withParameter(
                    new ControllerParameter($paramId, new Regex('/^[0-9]+$/'))
                )
                ->withParameter(
                    (new ControllerParameter($paramName, new Regex('/^\w+$/')))
                        ->withIsRequired(false)
                ),
            new Map([$paramId => '123'])
        );
        $this->assertFalse($controllerArguments->has($paramName));
    }
}
