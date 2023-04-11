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

use function Chevere\Parameter\integerp;
use Chevere\Parameter\Parameters;
use function Chevere\Parameter\stringp;
use Chevere\Parameter\StringParameter;
use Chevere\Throwable\Exceptions\OverflowException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $name = 'name';
        $parameters = new Parameters();
        $this->assertCount(0, $parameters);
        $this->assertCount(0, $parameters->optionalKeys());
        $this->assertCount(0, $parameters->requiredKeys());
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
        $this->assertCount(0, $parameters->optionalKeys());
        $this->assertCount(1, $parameters->requiredKeys());
        $parameters->assertHas($name);
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->isRequired($name));
        $this->assertSame($parameter, $parameters->get($name));
        $this->expectException(OverflowException::class);
        $parameters->withAddedRequired(...[
            $name => $parameter,
        ]);
    }

    public function testConstructPositional(): void
    {
        $parameters = new Parameters(
            stringp(),
            integerp(),
            integerp(),
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
        $this->assertCount(0, $parameters->optionalKeys());
        $this->assertCount(1, $parameters->requiredKeys());
        $parameters->assertHas($name);
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->isRequired($name));
        $this->assertSame($parameter, $parameters->get($name));
        $parametersWith = $parameters->withAddedRequired(test: $parameter);
        $this->assertNotSame($parameters, $parametersWith);
        $this->expectException(OverflowException::class);
        $parameters->withAddedRequired(...[
            $name => $parameter,
        ]);
    }

    public function testWithout(): void
    {
        $parameters = (new Parameters())
            ->withAddedRequired(
                a: stringp(),
                b: stringp(),
                c: stringp(),
            )
            ->withAddedOptional(
                x: stringp(),
                y: stringp(),
                z: stringp(),
            );
        $parametersWith = $parameters->without('a', 'y');
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(4, $parametersWith);
        $this->assertSame(['b', 'c'], $parametersWith->requiredKeys());
        $this->assertSame(['x', 'z'], $parametersWith->optionalKeys());
    }

    public function testWithAddedOptional(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters();
        $parametersWith = $parameters
            ->withAddedOptional(...[
                $name => $parameter,
            ]);
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(1, $parametersWith);
        $this->assertCount(1, $parametersWith->optionalKeys());
        $this->assertCount(0, $parametersWith->requiredKeys());
        $this->assertTrue($parametersWith->has($name));
        $this->assertTrue($parametersWith->isOptional($name));
        $this->assertFalse($parametersWith->isRequired($name));
        $this->assertSame($parameter, $parametersWith->get($name));
        $this->expectException(OverflowException::class);
        $parametersWith->withAddedOptional(...[
            $name => $parameter,
        ]);
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
}
