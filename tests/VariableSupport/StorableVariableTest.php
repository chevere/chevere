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

use function Chevere\Filesystem\fileForPath;
use Chevere\VariableSupport\Exceptions\UnableToStoreException;
use Chevere\VariableSupport\StorableVariable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class StorableVariableTest extends TestCase
{
    /**
     * @var resource
     */
    private $resource;

    protected function setUp(): void
    {
        $this->resource = fopen(__FILE__, 'r');
        if (! is_resource($this->resource)) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
    }

    protected function tearDown(): void
    {
        fclose($this->resource);
    }

    public function testNotExportable(): void
    {
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessageMatches('/ of type resource/');
        $storable = new StorableVariable($this->resource);
        $storable->toExport();
    }

    public function testContainsExportableClass(): void
    {
        $object = new stdClass();
        $array = [1, 2, 3, $object];
        $storable = new StorableVariable($array);
        $this->expectNotToPerformAssertions();
        $storable->toExport();
    }

    public function testContainsNotExportableClass(): void
    {
        $childObject = fileForPath(__FILE__);
        $object = new stdClass();
        $subObject = new stdClass();
        $subObject->file = [$childObject];
        $childType = $childObject::class;
        $object->string = 'test';
        $object->array = [1, 2, 3, $subObject];
        $atBreadcrumb =
            <<<STRING
            Object without __set_state method at [object: stdClass][property: \$array][(iterable)][key: 3][object: stdClass][property: \$file][(iterable)][key: 0][object: {$childType}]
            STRING;
        $storable = new StorableVariable($object);
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessage($atBreadcrumb);
        $storable->toExport();
    }

    public function testContainsNotExportableResource(): void
    {
        $resource = fopen(__FILE__, 'r');
        $array = [1, 2, 3, $resource];
        $atBreadcrumb =
            <<<STRING
            Argument contains a resource at [(iterable)][key: 3]
            STRING;
        $storable = new StorableVariable($array);
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessage($atBreadcrumb);
        $storable->toExport();
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
