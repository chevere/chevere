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
use Chevere\Components\Parameter\ParameterOptional;
use Chevere\Components\Parameter\ParameterRequired;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Parameter\ArgumentRegexMatchException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use PHPUnit\Framework\TestCase;

final class ArgumentsTest extends TestCase
{
    public function testInvalidArguments(): void
    {
        $parameters = (new Parameters)
            ->withAdded(
                new ParameterRequired('test')
            );
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, ['test' => 123]);
    }

    public function testConstruct(): void
    {
        $args = [
            'id' => '1',
            'name' => 'someValue',
        ];
        $parameters = (new Parameters)
            ->withAdded(
                new ParameterRequired('id')
            )
            ->withAdded(
                new ParameterRequired('name')
            );
        $arguments = new Arguments($parameters, $args);
        $this->assertSame($args, $arguments->toArray());
        foreach ($args as $name => $value) {
            $this->assertTrue($arguments->has($name));
            $this->assertSame($value, $arguments->get($name));
        }
        $notFoundKey = '404';
        $this->assertFalse($arguments->has($notFoundKey));
        $this->expectException(OutOfBoundsException::class);
        $arguments->get($notFoundKey);
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
                (new ParameterRequired('id'))
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
        $arguments = new Arguments(
            (new Parameters)
                ->withAdded(
                    (new ParameterRequired($name))
                        ->withRegex(new Regex('/^[0-9]+$/'))
                ),
            [$name => $value]
        );
        $this->assertTrue($arguments->has($name));
        $this->assertSame($value, $arguments->get($name));
        $arguments = $arguments->withArgument($name, $valueAlt);
        $this->assertSame($valueAlt, $arguments->get($name));
        $this->expectException(ArgumentRegexMatchException::class);
        $arguments->withArgument($name, 'invalid');
    }

    public function testArgumentsRequiredException(): void
    {
        $parameters = (new Parameters)
            ->withAdded(
                (new ParameterRequired('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
        $arguments = [];
        $this->expectException(ArgumentRequiredException::class);
        new Arguments($parameters, $arguments);
    }

    public function testParameterOptional(): void
    {
        $required = 'id';
        $optDefault = 'name';
        $arguments = new Arguments(
            (new Parameters)
                ->withAdded(
                    new ParameterRequired($required)
                )
                ->withAdded(
                    new ParameterOptional($optDefault)
                ),
            [$required => '123']
        );
        $this->assertFalse($arguments->has($optDefault));
    }

    public function testParameterOptionalDefault(): void
    {
        $required = 'id';
        $optDefault = 'name';
        $arguments = new Arguments(
            (new Parameters)
                ->withAdded(
                    (new ParameterRequired($required))
                )
                ->withAdded(
                    (new ParameterOptional($optDefault))
                        ->withRegex(new Regex('/^a|b$/'))
                        ->withDefault('a')
                ),
            [$required => '123']
        );
        $this->assertTrue($arguments->has($optDefault));
    }
}
