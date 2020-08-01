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

namespace Chevere\Tests\Parameter;

use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\ParameterOptional;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Parameter\ArgumentRegexMatchException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use PHPUnit\Framework\TestCase;

final class ArgumentsTest extends TestCase
{
    public function testConstruct(): void
    {
        $arguments = [
            'id' => '1',
            'name' => 'someValue',
        ];
        $parameters = (new Parameters)
            ->withAdded(
                (new Parameter('id'))
                    ->withRegex(new Regex('/^\d+$/'))
            )
            ->withAdded(
                (new Parameter('name'))
                    ->withRegex(new Regex('/^\w+$/'))
            );
        $controllerArguments = new Arguments($parameters, $arguments);
        $this->assertSame($arguments, $controllerArguments->toArray());
        foreach ($arguments as $name => $value) {
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
        $parameters = new Parameters;
        $this->expectException(OutOfBoundsException::class);
        new Arguments($parameters, ['id' => '123']);
    }

    public function testInvalidRegexArgument(): void
    {
        $parameters = (new Parameters)
            ->withAdded(
                (new Parameter('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
        $this->expectException(ArgumentRegexMatchException::class);
        (new Arguments($parameters, ['id' => 'abc']));
    }

    public function testPut(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $controllerArguments = new Arguments(
            (new Parameters)
                ->withAdded(
                    (new Parameter($name))
                        ->withRegex(new Regex('/^[0-9]+$/'))
                ),
            [$name => $value]
        );
        $this->assertTrue($controllerArguments->has($name));
        $this->assertSame($value, $controllerArguments->get($name));
        $controllerArguments = $controllerArguments->withArgument($name, $valueAlt);
        $this->assertSame($valueAlt, $controllerArguments->get($name));
        $this->expectException(ArgumentRegexMatchException::class);
        $controllerArguments->withArgument($name, 'invalid');
    }

    public function testArgumentsRequiredException(): void
    {
        $parameters = (new Parameters)
            ->withAdded(
                (new Parameter('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
        $arguments = [];
        $this->expectException(ArgumentRequiredException::class);
        new Arguments($parameters, $arguments);
    }

    public function testParameterOptional(): void
    {
        $paramId = 'id';
        $paramName = 'name';
        $controllerArguments = new Arguments(
            (new Parameters)
                ->withAdded(
                    (new Parameter($paramId))
                        ->withRegex(new Regex('/^[0-9]+$/'))
                )
                ->withAdded(
                    (new ParameterOptional($paramName))
                        ->withRegex(new Regex('/^\w+$/'))
                ),
            [$paramId => '123']
        );
        $this->assertFalse($controllerArguments->has($paramName));
    }
}
