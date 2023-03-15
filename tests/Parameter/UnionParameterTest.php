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

use function Chevere\Parameter\integerParameter;
use function Chevere\Parameter\stringParameter;
use Chevere\Parameter\UnionParameter;
use Chevere\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;

final class UnionParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new UnionParameter();
        $this->assertSame(
            TypeInterface::UNION,
            $parameter->getType()->primitive()
        );
        $this->assertCount(0, $parameter->parameters());
    }

    public function testWithAdded(): void
    {
        $parameter = new UnionParameter();
        $parameterOne = stringParameter();
        $parameterTwo = integerParameter();
        $parameterWith = $parameter->withAdded(
            $parameterOne,
            $parameterTwo
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->parameters());
    }
}
