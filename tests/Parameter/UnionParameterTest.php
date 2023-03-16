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
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringp;
use Chevere\Parameter\UnionParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;

final class UnionParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new UnionParameter(
            parameters()
        );
        $this->assertSame(
            TypeInterface::UNION,
            $parameter->getType()->primitive()
        );
        $this->assertCount(0, $parameter->parameters());
    }

    public function testWithAdded(): void
    {
        $parameter = new UnionParameter(
            parameters()
        );
        $one = stringp();
        $two = integerp();
        $parameterWith = $parameter->withAdded(
            $one,
            $two
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->parameters());
        $this->assertSame($one, $parameterWith->parameters()->get('0'));
        $this->assertSame($two, $parameterWith->parameters()->get('1'));
    }

    public function testAssertCompatible(): void
    {
        $parameters = parameters(
            stringp(),
        );
        $parametersAlt = parameters(
            stringp(description: 'one'),
        );
        $parameter = new UnionParameter($parameters);
        $compatible = new UnionParameter($parametersAlt);
        $this->expectNotToPerformAssertions();
        $parameter->assertCompatible($compatible);
    }

    public function testAssertNotCompatible(): void
    {
        $parameters = parameters(
            stringp(),
        );
        $parametersAlt = parameters(
            integerp(),
        );
        $parameter = new UnionParameter($parameters);
        $compatible = new UnionParameter($parametersAlt);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($compatible);
    }
}
