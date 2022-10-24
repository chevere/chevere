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

use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use PHPUnit\Framework\TestCase;

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
        $parameters->withAdded(...[
            $name => $parameter,
        ]);
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
        $parametersWithAdded = $parameters->withAdded(test: $parameter);
        $this->assertNotSame($parameters, $parametersWithAdded);
        $this->expectException(OverflowException::class);
        $parameters->withAdded(...[
            $name => $parameter,
        ]);
    }

    public function testWithAddedOptional(): void
    {
        $name = 'named';
        $parameter = new StringParameter();
        $parameters = new Parameters();
        $parametersWithAddedOptional = $parameters
            ->withAddedOptional(...[
                $name => $parameter,
            ]);
        $this->assertNotSame($parameters, $parametersWithAddedOptional);
        $this->assertCount(1, $parametersWithAddedOptional);
        $this->assertCount(1, $parametersWithAddedOptional->optional());
        $this->assertCount(0, $parametersWithAddedOptional->required());
        $this->assertTrue($parametersWithAddedOptional->has($name));
        $this->assertTrue($parametersWithAddedOptional->isOptional($name));
        $this->assertSame($parameter, $parametersWithAddedOptional->get($name));
        $this->expectException(OverflowException::class);
        $parametersWithAddedOptional->withAddedOptional(...[
            $name => $parameter,
        ]);
    }

    public function testIsRequiredOutOfBounds(): void
    {
        $parameters = new Parameters();
        $this->expectException(OutOfBoundsException::class);
        $parameters->isRequired('not-found');
    }

    public function testIsOptionalOutOfBounds(): void
    {
        $parameters = new Parameters();
        $this->expectException(OutOfBoundsException::class);
        $parameters->isOptional('not-found');
    }

    public function testWithModified(): void
    {
        $name = 'name';
        $parameters = new Parameters(name: new StringParameter());
        $parametersWithModify = $parameters
            ->withModify(
                name: (new StringParameter())->withDefault('eee')
            );
        $this->assertNotSame($parameters, $parametersWithModify);
        $parameters->assertHas($name);
        $this->assertTrue($parametersWithModify->has($name));
        $this->expectException(OutOfBoundsException::class);
        $parametersWithModify->withModify(notFound: new StringParameter());
    }
}
