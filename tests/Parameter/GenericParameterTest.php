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

use function Chevere\Parameter\arrayParameter;
use Chevere\Parameter\GenericParameter;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\GenericsInterface;
use function Chevere\Parameter\stringParameter;
use PHPUnit\Framework\TestCase;

final class GenericParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $value = stringParameter();
        $key = stringParameter();
        $description = 'test';
        $parameter = new GenericParameter(
            $value,
            $key,
            $description
        );
        $this->assertSame($value, $parameter->value());
        $this->assertSame($key, $parameter->key());
        $this->assertSame([], $parameter->default());
        $this->assertSame($description, $parameter->description());
        /** @var GenericParameterInterface $genericParameter */
        $genericParameter = $parameter->parameters()->get(GenericsInterface::GENERIC_NAME);
        $this->assertEquals($genericParameter, $parameter);
    }

    public function testAssertCompatible(): void
    {
        $this->expectNotToPerformAssertions();
        $key = stringParameter();
        $value = arrayParameter();
        $parameter = new GenericParameter($value, $key);
        $parameterAlt = new GenericParameter($value, $key, 'compatible');
        $parameterAlt->assertCompatible($parameter);
    }
}
