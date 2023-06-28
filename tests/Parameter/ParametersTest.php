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

use Chevere\Parameter\ArrayParameter;
use Chevere\Parameter\BooleanParameter;
use Chevere\Parameter\FileParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\ObjectParameter;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Throwable\Exceptions\OverflowException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;

final class ParametersTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $name = 'name';
        $parameters = new Parameters();
        $this->assertCount(0, $parameters);
        $this->assertCount(0, $parameters->optional());
        $this->assertCount(0, $parameters->required());
        $this->assertFalse($parameters->has($name));
        $this->expectException(OutOfBoundsException::class);
        $parameters->get($name);
    }

    public function testAssertEmpty(): void
    {
        $name = 'name';
        $parameters = new Parameters();
        $this->expectException(OutOfBoundsException::class);
        $parameters->assertHas($name);
    }

    public function testConstruct(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertCount(1, $parameters);
        $this->assertCount(0, $parameters->optional());
        $this->assertCount(1, $parameters->required());
        $parameters->assertHas($name);
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->isRequired($name));
        $this->assertSame($parameter, $parameters->get($name));
        $this->expectException(OverflowException::class);
        $parameters->withRequired(
            $name,
            $parameter,
        );
    }

    public function testConstructPositional(): void
    {
        $parameters = new Parameters(
            string(),
            integer(),
            integer(),
        );
        $this->assertCount(3, $parameters);
    }

    public function testWithAddedOverflow(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters(
            ...[
                $name => $parameter,
            ]
        );
        $this->assertCount(1, $parameters);
        $this->assertCount(0, $parameters->optional());
        $this->assertCount(1, $parameters->required());
        $parameters->assertHas($name);
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->isRequired($name));
        $this->assertSame($parameter, $parameters->get($name));
        $parametersWith = $parameters->withRequired('test', $parameter);
        $this->assertNotSame($parameters, $parametersWith);
        $this->expectException(OverflowException::class);
        $parameters->withRequired(
            $name,
            $parameter,
        );
    }

    public function testWithout(): void
    {
        $parameters = (new Parameters())
            ->withRequired('a', string())
            ->withRequired('b', string())
            ->withRequired('c', string())
            ->withOptional('x', string())
            ->withOptional('y', string())
            ->withOptional('z', string());
        $parametersWith = $parameters->without('a', 'y');
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(4, $parametersWith);
        $this->assertSame(['b', 'c'], $parametersWith->required()->toArray());
        $this->assertSame(['x', 'z'], $parametersWith->optional()->toArray());
    }

    public function testWithAddedOptional(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters();
        $parametersWith = $parameters->withOptional($name, $parameter);
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(1, $parametersWith);
        $this->assertCount(1, $parametersWith->optional());
        $this->assertCount(0, $parametersWith->required());
        $this->assertTrue($parametersWith->has($name));
        $this->assertTrue($parametersWith->isOptional($name));
        $this->assertFalse($parametersWith->isRequired($name));
        $this->assertSame($parameter, $parametersWith->get($name));
        $this->expectException(OverflowException::class);
        $parametersWith->withOptional($name, $parameter);
    }

    public function testIsRequiredOutOfRange(): void
    {
        $parameters = new Parameters();
        $this->expectException(OutOfBoundsException::class);
        $parameters->isRequired('not-found');
    }

    public function testIsOptionalOutOfRange(): void
    {
        $parameters = new Parameters();
        $this->expectException(OutOfBoundsException::class);
        $parameters->isOptional('not-found');
    }

    public function testGetArray(): void
    {
        $name = 'test';
        $parameter = new ArrayParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame($parameter, $parameters->getArray($name));
        $this->expectException(\TypeError::class);
        $parameters->getInteger($name);
    }

    public function testGetBoolean(): void
    {
        $name = 'test';
        $parameter = new BooleanParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame($parameter, $parameters->getBoolean($name));
        $this->expectException(\TypeError::class);
        $parameters->getInteger($name);
    }

    public function testGetFile(): void
    {
        $name = 'test';
        $parameter = new FileParameter(
            name: new StringParameter(),
            size: new IntegerParameter(),
            type: new StringParameter(),
            tmp_name: new StringParameter(),
        );
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame($parameter, $parameters->getFile($name));
        $this->expectException(\TypeError::class);
        $parameters->getInteger($name);
    }

    public function testGetFloat(): void
    {
        $name = 'test';
        $parameter = new FloatParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame($parameter, $parameters->getFloat($name));
        $this->expectException(\TypeError::class);
        $parameters->getInteger($name);
    }

    public function testGetObject(): void
    {
        $name = 'test';
        $parameter = new ObjectParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame($parameter, $parameters->getObject($name));
        $this->expectException(\TypeError::class);
        $parameters->getInteger($name);
    }

    public function testGetString(): void
    {
        $name = 'test';
        $parameter = new StringParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame($parameter, $parameters->getString($name));
        $this->expectException(\TypeError::class);
        $parameters->getInteger($name);
    }
}
