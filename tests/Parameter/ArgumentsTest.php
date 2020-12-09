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
use Chevere\Components\Parameter\ArrayParameter;
use Chevere\Components\Parameter\BooleanParameter;
use Chevere\Components\Parameter\FloatParameter;
use Chevere\Components\Parameter\IntegerParameter;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Parameter\ArgumentRegexMatchException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use Chevere\Interfaces\Type\TypeInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class ArgumentsTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $parameters = (new Parameters)
            ->withAddedRequired(test: new StringParameter);
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, test: 123);
    }

    public function testConstruct(): void
    {
        $args = [
            'id' => 1,
            'name' => 'someValue',
        ];
        $parameters = (new Parameters)
            ->withAddedRequired(
                id: new IntegerParameter,
                name: new StringParameter
            );
        $arguments = new Arguments($parameters, ...$args);
        $this->assertSame($parameters, $arguments->parameters());
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

    public function testInvalidParameterCount(): void
    {
        $parameters = new Parameters;
        $this->expectException(ArgumentCountException::class);
        new Arguments($parameters, id: '123');
    }

    public function testInvalidParameters(): void
    {
        $parameters = (new Parameters)
            ->withAddedRequired(test: new StringParameter);
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, id: '123');
    }

    public function testInvalidRegexArgument(): void
    {
        $parameters = (new Parameters)
            ->withAddedRequired(
                id: (new StringParameter)
                    ->withRegex(new Regex('/^[0-9]+$/')),
            );
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, id: 'abc');
    }

    public function testWithMissingArgument(): void
    {
        $name = 'test';
        $parameters = (new Parameters)
            ->withAddedRequired(test: new StringParameter);
        $arguments = new Arguments($parameters, ...[$name => '123']);
        $this->expectException(ArgumentRequiredException::class);
        $arguments->withArgument('not-found', 1234);
    }

    public function testWithArgument(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    id: (new StringParameter)
                        ->withRegex(new Regex('/^[0-9]+$/'))
                ),
            ...[$name => $value]
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
            ->withAddedRequired(
                id: (new StringParameter)
                        ->withRegex(new Regex('/^[0-9]+$/'))
            );
        $this->expectException(ArgumentCountException::class);
        new Arguments($parameters, ...[]);
    }

    public function testParameterOptional(): void
    {
        $required = 'id';
        $optional = 'name';
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    id: new StringParameter
                )
                ->withAddedOptional(
                    name: new StringParameter
                ),
            ...[$required => '123']
        );
        $this->assertFalse($arguments->has($optional));
    }

    public function testParameterDefault(): void
    {
        $required = 'id';
        $optional = 'name';
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    id: new StringParameter
                )
                ->withAddedOptional(
                    name: (new StringParameter)
                            ->withRegex(new Regex('/^a|b$/'))
                            ->withDefault('a')
                ),
            ...[$required => '123']
        );
        $this->assertTrue($arguments->has($optional));
    }

    public function testParameter(): void
    {
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        $getters = ['boolean', 'string', 'integer', 'float', 'array'];
        foreach ([
            TypeInterface::BOOLEAN => true,
            TypeInterface::INTEGER => 1,
            TypeInterface::FLOAT => 13.13,
            TypeInterface::STRING => 'test',
            TypeInterface::ARRAY => ['test'],
            TypeInterface::OBJECT => new stdClass,
            TypeInterface::CALLABLE => 'phpinfo',
            TypeInterface::ITERABLE => [4, 2, 1, 3],
            TypeInterface::RESOURCE => $resource,
        ] as $type => $value) {
            $name = 'test-' . $type;
            $arguments = new Arguments(
                (new Parameters)
                    ->withAddedRequired(
                        ...[$name => new Parameter(new Type($type))]
                    ),
                ...[$name => $value]
            );
            $this->assertSame($value, $arguments->get($name));
            if (in_array($type, $getters)) {
                $getter = 'get' . ucfirst($type);
                $this->assertSame($value, $arguments->{$getter}($name));
            }
        }
        /** @var resource $resource */
        fclose($resource);
    }

    public function testGetBoolean(): void
    {
        $name = 'test';
        $var = true;
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(test: new BooleanParameter),
            ...[$name => $var]
        );
        $this->assertSame($var, $arguments->getBoolean($name));
        $this->expectException(TypeError::class);
        $arguments->getString($name);
    }

    public function testGetString(): void
    {
        $name = 'test';
        $var = 'string';
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(test: new StringParameter),
            ...[$name => $var]
        );
        $this->assertSame($var, $arguments->getString($name));
        $this->expectException(TypeError::class);
        $arguments->getBoolean($name);
    }

    public function testGetInteger(): void
    {
        $name = 'test';
        $var = 1234;
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    test: new IntegerParameter
                ),
            ...[$name => $var]
        );
        $this->assertSame($var, $arguments->getInteger($name));
        $this->expectException(TypeError::class);
        $arguments->getArray($name);
    }

    public function testGetFloat(): void
    {
        $name = 'test';
        $var = 12.34;
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    test: new FloatParameter($name)
                ),
            ...[$name => $var]
        );
        $this->assertSame($var, $arguments->getFloat($name));
        $this->expectException(TypeError::class);
        $arguments->getArray($name);
    }

    public function testGetArray(): void
    {
        $name = 'test';
        $var = [1, 2, '3'];
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    test: new ArrayParameter
                ),
            ...[$name => $var]
        );
        $this->assertSame($var, $arguments->getArray($name));
        $this->expectException(TypeError::class);
        $arguments->getInteger($name);
    }
}
