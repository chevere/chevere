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

use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\ReflectionParameterTyped;
use Chevere\Tests\Parameter\_resources\Depends;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionParameter;

final class ReflectionParameterTypedTest extends TestCase
{
    public function testParameterObject(): void
    {
        $parameter = $this->getReflection('useObject');
        $reflection = new ReflectionParameterTyped($parameter);
        $this->assertInstanceOf(ObjectParameterInterface::class, $reflection->parameter());
        $this->assertSame(null, $reflection->default());
    }

    public function testParameterDefault(): void
    {
        $parameter = $this->getReflection('useString');
        $reflection = new ReflectionParameterTyped($parameter);
        $this->assertInstanceOf(StringParameterInterface::class, $reflection->parameter());
        $this->assertSame('default', $reflection->default());
    }

    public function testUnion(): void
    {
        $parameter = $this->getReflection('useUnion');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$union of type union is not supported');
        new ReflectionParameterTyped($parameter);
    }

    private function getReflection(string $method, int $pos = 0): ReflectionParameter
    {
        $reflection = new ReflectionMethod(Depends::class, $method);

        return $reflection->getParameters()[$pos];
    }
}
