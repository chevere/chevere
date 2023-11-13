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

use Chevere\Parameter\UnionParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\int;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;
use function Chevere\Parameter\union;

final class UnionParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new UnionParameter(
            parameters()
        );
        $this->assertSame(
            TypeInterface::UNION,
            $parameter->type()->primitive()
        );
        $this->assertCount(0, $parameter->parameters());
        $this->assertSame(TypeInterface::UNION, $parameter->typeSchema());
    }

    public function testWithAdded(): void
    {
        $parameter = new UnionParameter(
            parameters()
        );
        $one = string();
        $two = int();
        $with = $parameter->withAdded($one, $two);
        $this->assertNotSame($parameter, $with);
        $this->assertCount(2, $with->parameters());
        $this->assertSame($one, $with->parameters()->get('0'));
        $this->assertSame($two, $with->parameters()->get('1'));
    }

    public function testAssertCompatible(): void
    {
        $parameters = parameters(
            string(),
        );
        $parametersAlt = parameters(
            string(description: 'one'),
        );
        $parameter = new UnionParameter($parameters);
        $compatible = new UnionParameter($parametersAlt);
        $this->expectNotToPerformAssertions();
        $parameter->assertCompatible($compatible);
    }

    public function testAssertNotCompatible(): void
    {
        $parameters = parameters(
            string(),
        );
        $parametersAlt = parameters(
            int(),
        );
        $parameter = new UnionParameter($parameters);
        $compatible = new UnionParameter($parametersAlt);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($compatible);
    }

    public function testUnionArguments(): void
    {
        $parameter = union(
            string(),
            int()
        );
        $array = [
            0 => 'foo',
            1 => 1,
        ];
        $arguments = arguments($parameter, $array);
        $this->assertSame($array['0'], $arguments->required('0')->string());
        $this->assertSame($array['1'], $arguments->required('1')->int());
    }

    public function testInvoke(): void
    {
        $parameter = union(
            string(),
            int()
        );
        $this->assertSame(10, $parameter(10));
        $this->assertSame('10', $parameter('10'));
    }
}
