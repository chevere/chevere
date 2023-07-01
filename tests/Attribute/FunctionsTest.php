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
        return [
            [
                new ReflectionClass(ClassUsesDescription::class),
                'Class',
            ],
            [
                new ReflectionMethod(ClassUsesDescription::class, 'run'),
                'Method',
            ],
            [
                new ReflectionProperty(ClassUsesDescription::class, 'property'),
                'Property',
            ],
            [
                new ReflectionParameter([ClassUsesDescription::class, 'run'], 'parameter'),
                'Parameter',
            ],
            [
                new ReflectionClassConstant(ClassUsesDescription::class, 'CONSTANT'),
                'Constant',
            ],
        ];
    }

    /**
     * @dataProvider classDataProvider
     */
    public function testClass($reflection, string $meta): void
    {
        $description = getDescription($reflection);
        $this->assertSame($meta, $description->__toString());
    }

    public function functionDataProvider(): array
    {
        $fqn = 'Chevere\Tests\Attribute\_resources\functionUsesDescription';

        return [
            [
                new ReflectionFunction($fqn),
                'Function',
            ],
            [
                new ReflectionParameter($fqn, 'parameter'),
                'Parameter',
            ],
        ];
    }

    /**
     * @dataProvider functionDataProvider
     */
    public function testFunction($reflection, string $meta): void
    {
        new ClassUsesDescription();
        $description = getDescription($reflection);
        $this->assertSame($meta, $description->__toString());
    }
}
