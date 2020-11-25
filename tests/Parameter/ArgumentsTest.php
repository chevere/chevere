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
    public function testInvalidArguments(): void
    {
        $parameters = (new Parameters)
            ->withAddedRequired(
                new StringParameter('test')
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
            ->withAddedRequired(
                new StringParameter('id')
            )
            ->withAddedRequired(
                new StringParameter('name')
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

    public function testInvalidArgumentName(): void
    {
        $parameters = new Parameters;
        $this->expectException(OutOfBoundsException::class);
        new Arguments($parameters, ['id' => '123']);
    }

    public function testInvalidRegexArgument(): void
    {
        $parameters = (new Parameters)
            ->withAddedRequired(
                (new StringParameter('id'))
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
                ->withAddedRequired(
                    (new StringParameter($name))
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
            ->withAddedRequired(
                (new StringParameter('id'))
                    ->withRegex(new Regex('/^[0-9]+$/'))
            );
        $arguments = [];
        $this->expectException(ArgumentRequiredException::class);
        new Arguments($parameters, $arguments);
    }

    public function testParameterOptional(): void
    {
        $required = 'id';
        $optional = 'name';
        $arguments = new Arguments(
            (new Parameters)
                ->withAddedRequired(
                    new StringParameter($required)
                )
                ->withAddedOptional(
                    new StringParameter($optional)
                ),
            [$required => '123']
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
                    (new StringParameter($required))
                )
                ->withAddedOptional(
                    (new StringParameter($optional))
                        ->withRegex(new Regex('/^a|b$/'))
                        ->withDefault('a')
                ),
            [$required => '123']
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
                        new Parameter($name, new Type($type))
                    ),
                [$name => $value]
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
                ->withAddedRequired(
                    new BooleanParameter($name)
                ),
            [$name => $var]
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
                ->withAddedRequired(
                    new StringParameter($name)
                ),
            [$name => $var]
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
                    new IntegerParameter($name)
                ),
            [$name => $var]
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
                    new FloatParameter($name)
                ),
            [$name => $var]
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
                    new ArrayParameter($name)
                ),
            [$name => $var]
        );
        $this->assertSame($var, $arguments->getArray($name));
        $this->expectException(TypeError::class);
        $arguments->getInteger($name);
    }
}
