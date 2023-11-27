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

use Chevere\Parameter\GenericParameter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\assertGeneric;
use function Chevere\Parameter\generic;
use function Chevere\Parameter\int;
use function Chevere\Parameter\string;
use function Chevere\Parameter\union;

final class GenericParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $value = string();
        $key = string();
        $description = 'test';
        $parameter = new GenericParameter(
            $value,
            $key,
            $description
        );
        $this->assertSame($value, $parameter->value());
        $this->assertSame($key, $parameter->key());
        $this->assertSame(null, $parameter->default());
        $this->assertSame($description, $parameter->description());
        $this->assertSame([
            'type' => 'generic',
            'description' => $description,
            'default' => null,
            'parameters' => [
                'K' => [
                    'required' => true,
                ] + $key->schema(),
                'V' => [
                    'required' => true,
                ] + $value->schema(),
            ],
        ], $parameter->schema());
        $parameters = $parameter->parameters();
        $this->assertSame($parameters, $parameter->parameters());
        $this->assertEquals($value, $parameters->get('V'));
        $this->assertEquals($key, $parameters->get('K'));
    }

    public function testAssertCompatible(): void
    {
        $this->expectNotToPerformAssertions();
        $key = string();
        $value = int(description: 'compatible');
        $keyAlt = string(description: 'compatible');
        $valueAlt = int();
        $parameter = new GenericParameter($value, $key);
        $compatible = new GenericParameter($valueAlt, $keyAlt, 'compatible');
        $parameter->assertCompatible($compatible);
    }

    public function testAssertCompatibleConflictValue(): void
    {
        $key = string();
        $value = int();
        $valueAlt = int(min: 1);
        $parameter = new GenericParameter($value, $key);
        $notCompatible = new GenericParameter($valueAlt, $key);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleConflictKey(): void
    {
        $key = string();
        $value = int();
        $keyAlt = string('/^[a-z]+&/');
        $parameter = new GenericParameter($value, $key);
        $notCompatible = new GenericParameter($value, $keyAlt);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testNestedGeneric(): void
    {
        $this->expectNotToPerformAssertions();
        $parameter = generic(
            V: string(),
            K: string()
        );
        $argument = [
            'a' => 'A',
        ];
        assertGeneric($parameter, $argument);
        $parameter = generic(
            V: $parameter,
        );
        $argument = [
            [
                'b' => 'B',
            ],
            [
                'c' => 'C',
            ],
        ];
        assertGeneric($parameter, $argument);
    }

    public function testGenericArguments(): void
    {
        $parameter = generic(
            V: string(),
            K: int()
        );
        $array = [
            0 => 'foo',
            1 => 'bar',
            2 => 'baz',
        ];
        $arguments = arguments($parameter, $array);
        $this->assertSame($array['0'], $arguments->required('0')->string());
        $parameter = generic(
            V: generic(
                string()
            ),
            K: string()
        );
        $array = [
            '0' => ['foo', 'oof'],
            '1' => ['bar'],
            '2' => ['baz', 'bar'],
        ];
        $arguments = arguments($parameter, $array);
        $this->assertSame($array['0'], $arguments->required('0')->array());
    }

    public function testInvoke(): void
    {
        $value = [10, '10'];
        $parameter = generic(union(int(), string()));
        $this->assertSame($value, $parameter($value));
    }
}
