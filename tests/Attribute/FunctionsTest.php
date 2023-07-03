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

namespace Chevere\Tests\Attribute;

use Chevere\Tests\Attribute\_resources\ClassUsesDescription;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use function Chevere\Attribute\getDescription;

final class FunctionsTest extends TestCase
{
    public function classDataProvider(): array
    {
        $class = ClassUsesDescription::class;
        $function = 'Chevere\Tests\Attribute\_resources\functionUsesDescription';

        return [
            [
                new ReflectionClass($class),
                'Class',
            ],
            [
                new ReflectionMethod($class, 'run'),
                'Method',
            ],
            [
                new ReflectionProperty($class, 'property'),
                'Property',
            ],
            [
                new ReflectionParameter([$class, 'run'], 'parameter'),
                'Parameter',
            ],
            [
                new ReflectionClassConstant($class, 'CONSTANT'),
                'Constant',
            ],
            [
                new ReflectionFunction($function),
                'Function',
            ],
            [
                new ReflectionParameter($function, 'parameter'),
                'Parameter',
            ],
        ];
    }

    /**
     * @dataProvider classDataProvider
     */
    public function testGetDescription($reflection, string $meta): void
    {
        $description = getDescription($reflection);
        $this->assertSame($meta, $description->__toString());
    }
}