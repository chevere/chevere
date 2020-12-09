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

use BadFunctionCallException;
use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use PHPUnit\Framework\TestCase;

final class StringParameterTest extends TestCase
{

    public function testConstruct(): void
    {
        $name = 'parameter';
        $regex = '/^.*$/';
        $parameter = new StringParameter;
        $this->assertSame($regex, $parameter->regex()->toString());
    }

    public function testWithRegex(): void
    {
        $regex = new Regex('/^[0-9+]$/');
        $parameter = (new StringParameter)->withRegex($regex);
        $this->assertSame($regex->toString(), $parameter->regex()->toString());
    }

    public function testWithDescription(): void
    {
        $description = 'ola k ase';
        $parameter = new StringParameter('test');
        $this->assertSame('', $parameter->description());
        $parameter = $parameter->withDescription($description);
        $this->assertSame($description, $parameter->description());
    }

    public function testWithAddedAttribute(): void
    {
        $attribute = 'attribute';
        $parameter = new StringParameter('test');
        $this->assertCount(0, $parameter->attributes());
        $parameter = $parameter->withAddedAttribute($attribute);
        $this->assertCount(1, $parameter->attributes());
        $this->assertTrue($parameter->hasAttribute($attribute));
        $this->assertFalse($parameter->hasAttribute('wrong-name'));
        $this->expectException(OverflowException::class);
        $parameter->withAddedAttribute($attribute);
    }

    public function testWithRemovedAttribute(): void
    {
        $attribute = 'attribute';
        $parameter = (new StringParameter('test'))
            ->withAddedAttribute($attribute);
        $parameter = $parameter->withRemovedAttribute($attribute);
        $this->assertCount(0, $parameter->attributes());
        $this->assertFalse($parameter->hasAttribute($attribute));
        $this->expectException(OutOfBoundsException::class);
        $parameter->withRemovedAttribute($attribute);
    }

    public function testWithDefault(): void
    {
        $parameter = new StringParameter('test');
        $this->assertSame('', $parameter->default());
        $default = 'some value';
        $parameter = $parameter->withDefault($default);
        $this->assertSame($default, $parameter->default());
    }

    public function testWithDefaultRegexAware(): void
    {
        $parameter = (new StringParameter('test'))
            ->withRegex(new Regex('/^a|b$/'))
            ->withDefault('a');
        $this->expectException(InvalidArgumentException::class);
        $parameter->withDefault('');
    }
}
