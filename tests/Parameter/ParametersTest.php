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

use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Tests\src\ObjectHelper;
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
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->isRequired($name));
        $this->assertSame($parameter, $parameters->get($name));
        $this->expectException(OverflowException::class);
        $parameters->withAdded(...[
            $name => $parameter,
        ]);
    }

    public function testClone(): void
    {
        $parameters = new Parameters(test: new StringParameter());
        $clone = clone $parameters;
        $this->assertNotSame($parameters, $clone);
        $parametersHelper = new ObjectHelper($parameters);
        $cloneHelper = new ObjectHelper($clone);
        $this->assertNotSame(
            $parametersHelper->getPropertyValue('map'),
            $cloneHelper->getPropertyValue('map')
        );
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
            ->withAddedOptional(...[$name => $parameter]);
        $this->assertNotSame($parameters, $parametersWithAddedOptional);
        $this->assertCount(1, $parametersWithAddedOptional);
        $this->assertCount(1, $parametersWithAddedOptional->optional());
        $this->assertCount(0, $parametersWithAddedOptional->required());
        $this->assertTrue($parametersWithAddedOptional->has($name));
        $this->assertTrue($parametersWithAddedOptional->isOptional($name));
        $this->assertSame($parameter, $parametersWithAddedOptional->get($name));
        $this->expectException(OverflowException::class);
        $parametersWithAddedOptional->withAddedOptional(...[$name => $parameter]);
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
                name: (new StringParameter())->withAddedAttribute(attr: 'modify')
            );
        $this->assertNotSame($parameters, $parametersWithModify);
        $this->assertTrue($parametersWithModify->has($name));
        $this->assertTrue($parametersWithModify->get($name)->hasAttribute('attr'));
        $this->expectException(OutOfBoundsException::class);
        $parametersWithModify->withModify(notFound: new StringParameter());
    }
}
