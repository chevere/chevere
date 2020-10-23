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

use Chevere\Components\Parameter\ParameterRequired;
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    public function testEmptyName(): void
    {
        $this->expectException(ParameterNameInvalidException::class);
        new ParameterRequired('');
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(ParameterNameInvalidException::class);
        new ParameterRequired(' ');
    }

    public function testSpaceInName(): void
    {
        $this->expectException(ParameterNameInvalidException::class);
        new ParameterRequired('some name');
    }

    public function testConstruct(): void
    {
        $name = 'parameter';
        $regex = '/^[0-9+]$/';
        $parameter = new ParameterRequired($name);
        $this->assertSame($name, $parameter->name());
        $this->assertSame($regex, $parameter
            ->withRegex(new Regex($regex))->regex()->toString());
    }

    public function testWithDescription(): void
    {
        $description = 'ola k ase';
        $parameter = new ParameterRequired('test');
        $this->assertSame('', $parameter->description());
        $parameter = $parameter->withDescription($description);
        $this->assertSame($description, $parameter->description());
    }

    public function testWithAddedAttribute(): void
    {
        $attribute = 'attribute';
        $parameter = new ParameterRequired('test');
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
        $parameter = (new ParameterRequired('test'))
            ->withAddedAttribute($attribute);
        $parameter = $parameter->withRemovedAttribute($attribute);
        $this->assertCount(0, $parameter->attributes());
        $this->assertFalse($parameter->hasAttribute($attribute));
        $this->expectException(OutOfBoundsException::class);
        $parameter->withRemovedAttribute($attribute);
    }

    public function testWithDefault(): void
    {
        $parameter = new ParameterRequired('test');
        $this->assertSame('', $parameter->default());
        $default = 'some value';
        $parameter = $parameter->withDefault($default);
        $this->assertSame($default, $parameter->default());
    }

    public function testWithDefaultRegexAware(): void
    {
        $parameter = (new ParameterRequired('test'))
            ->withRegex(new Regex('/^a|b$/'))
            ->withDefault('a');
        $this->expectException(InvalidArgumentException::class);
        $parameter->withDefault('');
    }
}
