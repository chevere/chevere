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

namespace Chevere\Tests\VarSupport;

use Chevere\VarSupport\Exceptions\VarStorableException;
use Chevere\VarSupport\VarStorable;
use Chevere\Tests\VarSupport\_resources\ClassWithResource;
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
        $this->expectException(VarStorableException::class);
        $this->expectExceptionMessageMatches('/ of type resource/');
        /** @var resource $resource */
        new VarStorable($resource);
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
        $this->expectException(VarStorableException::class);
        $this->expectExceptionMessage($atString);
        new VarStorable($object);
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
            $varStorable = new VarStorable($val);
            $this->assertSame($val, $varStorable->var());
            $this->assertSame(serialize($val), $varStorable->toSerialize());
            $this->assertSame(var_export($val, true), $varStorable->toExport());
        }
    }
}
