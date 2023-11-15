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

use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\StringParameter;
use Chevere\Regex\Regex;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\string;

final class StringParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $regex = StringParameterInterface::REGEX_DEFAULT;
        $parameter = new StringParameter();
        $this->assertSame(null, $parameter->default());
        $this->assertSame('', $parameter->description());
        $this->assertEquals($parameter, string());
        $this->assertSame($regex, $parameter->regex()->__toString());
        $this->assertSame([
            'type' => 'string',
            'description' => '',
            'default' => null,
            'regex' => $parameter->regex()->noDelimiters(),
        ], $parameter->schema());
        $description = 'ola k ase';
        $parameter = new StringParameter($description);
        $this->assertSame($description, $parameter->description());
    }

    public function testWithRegex(): void
    {
        $regex = new Regex('/^[0-9+]$/');
        $parameter = (new StringParameter())->withRegex($regex);
        $this->assertSame($regex->__toString(), $parameter->regex()->__toString());
        $this->assertSame([
            'type' => 'string',
            'description' => '',
            'default' => null,
            'regex' => $regex->noDelimiters(),
        ], $parameter->schema());
    }

    public function testWithDescription(): void
    {
        $parameter = new StringParameter();
        $try = 'description';
        $this->assertSame('', $parameter->description());
        $parameterWith = $parameter->withDescription($try);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($try, $parameterWith->description());
    }

    public function testWithDefault(): void
    {
        $default = 'some value';
        $parameter = new StringParameter('test');
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

    public function testAssertCompatible(): void
    {
        $regex = new Regex('/^[0-9+]$/');
        $regexAlt = new Regex('/^[a-z+]$/');
        $parameter = (new StringParameter())->withRegex($regex);
        $compatible = (new StringParameter())->withRegex($regex);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = (new StringParameter())->withRegex($regexAlt);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testInvoke(): void
    {
        $value = '10';
        $parameter = new StringParameter();
        $this->assertSame($value, $parameter($value));
    }
}
