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

use function Chevere\Parameter\assertGeneric;
use function Chevere\Parameter\generic;
use Chevere\Parameter\GenericParameter;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

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
        $value = integer(description: 'compatible');
        $keyAlt = string(description: 'compatible');
        $valueAlt = integer();
        $parameter = new GenericParameter($value, $key);
        $compatible = new GenericParameter($valueAlt, $keyAlt, 'compatible');
        $parameter->assertCompatible($compatible);
    }

    public function testAssertCompatibleConflictValue(): void
    {
        $key = string();
        $value = integer();
        $valueAlt = integer(minimum: 1);
        $parameter = new GenericParameter($value, $key);
        $notCompatible = new GenericParameter($valueAlt, $key);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleConflictKey(): void
    {
        $key = string();
        $value = integer();
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
}
