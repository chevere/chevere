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

use function Chevere\Parameter\arrayParameter;
use Chevere\Parameter\GenericParameter;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use function Chevere\Parameter\stringParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GenericParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new GenericParameter();
        $this->assertInstanceOf(IntegerParameterInterface::class, $parameter->key());
        $this->assertInstanceOf(StringParameterInterface::class, $parameter->value());
        $this->assertSame([], $parameter->default());
    }

    public function testWithKey(): void
    {
        $parameter = new GenericParameter();
        $key = stringParameter();
        $parameterWith = $parameter->withKey($key);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($key, $parameterWith->key());
    }

    public function testWithValue(): void
    {
        $parameter = new GenericParameter();
        $value = arrayParameter();
        $parameterWith = $parameter->withValue($value);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($value, $parameterWith->value());
    }

    public function testAssertCompatible(): void
    {
        $this->expectNotToPerformAssertions();
        $parameter = new GenericParameter();
        $key = stringParameter();
        $value = arrayParameter();
        $parameterWith = $parameter->withKey($key)->withValue($value);
        $parameterWith->assertCompatible(
            $parameter
                ->withKey(stringParameter(description: 'compatible'))
                ->withValue(arrayParameter())
        );
    }

    public function methodProvider(): array
    {
        return [
            ['withKey'],
            ['withValue'],
        ];
    }

    /**
     * @dataProvider methodProvider
     */
    public function testAssertCompatibleConflict(string $method): void
    {
        $parameter = new GenericParameter();
        $compatible = stringParameter();
        $notCompatible = stringParameter('/^[a-z]+$/');
        $parameterWith = $parameter->{$method}($compatible);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected regex');
        $parameterWith->assertCompatible(
            $parameter->{$method}($notCompatible)
        );
    }
}
