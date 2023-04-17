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
use function Chevere\Parameter\string;
use Chevere\Parameter\StringParameter;
use Chevere\Regex\Regex;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StringParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $regex = StringParameterInterface::REGEX_DEFAULT;
        $parameter = new StringParameter();
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->description());
        $this->assertEquals($parameter, string());
        $this->assertSame($regex, $parameter->regex()->__toString());
        $this->assertSame([
            'type' => 'string',
            'description' => null,
            'default' => null,
            'regex' => $regex,
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
    }

    public function testWithDescription(): void
    {
        $description = 'ola k ase';
        $parameter = new StringParameter('test');
        $this->assertSame('test', $parameter->description());
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
}
