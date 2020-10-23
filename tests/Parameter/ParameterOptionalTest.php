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

use Chevere\Components\Parameter\ParameterOptional;
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ParameterOptionalTest extends TestCase
{
    public function testWithDefault(): void
    {
        $parameter = new ParameterOptional('test');
        $this->assertSame('', $parameter->default());
        $default = 'some value';
        $parameter = $parameter->withDefault($default);
        $this->assertSame($default, $parameter->default());
    }

    public function testWithDefaultRegexAware(): void
    {
        $parameter = (new ParameterOptional('test'))
            ->withRegex(new Regex('/^a|b$/'))
            ->withDefault('a');
        $this->expectException(InvalidArgumentException::class);
        $parameter->withDefault('');
    }
}
