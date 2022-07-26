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

namespace Chevere\Tests\VariableSupport;

use Chevere\Tests\VariableSupport\_resources\ClassWithResource;
use Chevere\VariableSupport\Exceptions\UnableToStoreException;
use Chevere\VariableSupport\StorableVariable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarStorableTest extends TestCase
{
    public function testNotExportable(): void
    {
        $resource = fopen(__FILE__, 'r');
        if (!is_resource($resource)) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessageMatches('/ of type resource/');
        /** @var resource $resource */
        new StorableVariable($resource);
        fclose($resource);
    }

    public function testContainsNotExportable(): void
    {
        $object = new stdClass();
        $resource = fopen(__FILE__, 'r');
        if (!is_resource($resource)) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        $childObject = new ClassWithResource($resource);
        $object->array = [1, 2, 3, $childObject];
        $atBreadcrumb = [
            'object: stdClass',
            'property: $array',
            '(iterable)',
            'key: 3',
            'object: ' . $childObject::class,
            'property: array $array',
            '(iterable)',
            'key: 0',
        ];
        $atString = '[' . implode('][', $atBreadcrumb) . ']';
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessage($atString);
        new StorableVariable($object);
        /** @var resource $resource */
        fclose($resource);
    }

    public function testConstruct(): void
    {
        foreach ([
            1,
            1.1,
            true,
            'test',
            [1, 2, 3],
            [1, 1.1, true, 'test'],
            [[1, 1.1, true, 'test']],
            new stdClass(),
            ['test', [1, false], new stdClass()],
        ] as $val) {
            $storableVariable = new StorableVariable($val);
            $this->assertSame($val, $storableVariable->variable());
            $this->assertSame(serialize($val), $storableVariable->toSerialize());
            $this->assertSame(var_export($val, true), $storableVariable->toExport());
        }
    }
}
