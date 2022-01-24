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

use Chevere\Parameter\StringParameter;
use function Chevere\Parameter\stringParameter;
use Chevere\Regex\Regex;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use PHPUnit\Framework\TestCase;

final class StringParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $regex = '/^.*$/';
        $parameter = new StringParameter();
        $this->assertEquals($parameter, stringParameter());
        $this->assertSame($regex, $parameter->regex()->__toString());
        $parameter->attributes();
    }

    public function testWithRegex(): void
    {
        $regex = new Regex('/^[0-9+]$/');
        $parameter = (new StringParameter())->withRegex($regex);
        $this->assertSame($regex->__toString(), $parameter->regex()->__toString());
    }

    public function testWithDescription(): void
    {
        $description = 'ola k ase';
        $parameter = new StringParameter('test');
        $this->assertSame('test', $parameter->description());
    }

    public function testWithAddedAttribute(): void
    {
        $attribute = 'attribute';
        $attributeValue = 'value';
        $parameter = new StringParameter('test');
        $this->assertCount(0, $parameter->attributes());
        $this->assertFalse($parameter->hasAttribute('empty'));
        $parameter = $parameter->withAddedAttribute(...[
            $attribute => $attributeValue,
        ]);
        $this->assertCount(1, $parameter->attributes());
        $this->assertTrue($parameter->hasAttribute($attribute));
        $this->assertFalse($parameter->hasAttribute('wrong-name'));
        $this->expectException(OverflowException::class);
        $parameter->withAddedAttribute(...[
            $attribute => 'some-value',
        ]);
    }

    public function testWithRemovedAttribute(): void
    {
        $attribute = 'attribute';
        $parameter = (new StringParameter('test'))
            ->withAddedAttribute($attribute);
        $parameter = $parameter->withoutAttribute($attribute);
        $this->assertCount(0, $parameter->attributes());
        $this->assertFalse($parameter->hasAttribute($attribute));
        $this->expectException(OutOfBoundsException::class);
        $parameter->withoutAttribute($attribute);
    }

    public function testWithDefault(): void
    {
        $parameter = new StringParameter('test');
        $this->assertSame('', $parameter->default());
        $default = 'some value';
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'string',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
    }

    public function testWithDefaultRegexAware(): void
    {
        $parameter = (new StringParameter('test'))->withDefault('a');
        $parameterWithRegex = $parameter
            ->withRegex(new Regex('/^a|b$/'));
        $this->assertNotSame($parameter, $parameterWithRegex);
        $this->expectException(InvalidArgumentException::class);
        $parameterWithRegex->withDefault('');
    }
}
