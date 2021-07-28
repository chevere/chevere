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

use function Chevere\Components\Parameter\objectParameter;
use function Chevere\Components\Parameter\parameters;
use function Chevere\Components\Parameter\stringParameter;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ParameterFunctionsTest extends TestCase
{
    public function testFunctionParameters(): void
    {
        $parameters = parameters();
        $this->assertCount(0, $parameters);
        $parameters = parameters(
            foo: stringParameter()
        );
        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters->isRequired('foo'));
    }

    public function testFunctionObjectParameter(): void
    {
        $parameter = objectParameter(stdClass::class);
        $this->assertSame('', $parameter->description());
        $this->assertSame(stdClass::class, $parameter->className());
        $parameter = objectParameter(stdClass::class, 'foo');
        $this->assertSame('foo', $parameter->description());
    }

    public function testFunctionStringParameter(): void
    {
        $description = 'some description';
        $default = 'abcd';
        $regex = '/^[a-z]+$/';
        $parameter = stringParameter(
            description: $description,
            default: $default,
            regex: $regex,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame($regex, $parameter->regex()->toString());
    }
}
