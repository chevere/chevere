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

use Chevere\Parameter\ArrayStringParameter;
use function Chevere\Parameter\string;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArrayStringParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ArrayStringParameter();
        $this->assertCount(0, $parameter->parameters());
    }

    public function testWithRequired(): void
    {
        $parameter = new ArrayStringParameter();
        $foo = string();
        $parameterWith = $parameter->withRequired(
            foo: $foo
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(1, $parameterWith->parameters());
        $this->assertSame($foo, $parameterWith->parameters()->get('foo'));
        $this->assertTrue($parameterWith->parameters()->isRequired('foo'));
    }

    public function testWithOptional(): void
    {
        $parameter = new ArrayStringParameter();
        $foo = string();
        $parameterWith = $parameter->withOptional(
            foo: $foo
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(1, $parameterWith->parameters());
        $this->assertSame($foo, $parameterWith->parameters()->get('foo'));
        $this->assertTrue($parameterWith->parameters()->isOptional('foo'));
    }

    public function testAssertCompatible(): void
    {
        $test = new ArrayStringParameter();
        $test->assertCompatible(new ArrayStringParameter());
        $notCompatible = (new ArrayStringParameter())->withRequired(
            foo: string()
        );
        $this->expectException(InvalidArgumentException::class);
        $test->assertCompatible($notCompatible);
    }
}
