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

use Chevere\Tests\VariableSupport\_resources\ClassWithPropertyNotExportable;
use Chevere\VariableSupport\Exceptions\UnableToStoreException;
use Chevere\VariableSupport\StorableVariable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\VarExporter\VarExporter;

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

    public function testNotSerializable(): void
    {
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessageMatches('/ of type resource/');
        $storable = new StorableVariable($this->resource);
        $storable->toSerialize();
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
        $file = fopen(__FILE__, 'r');
        $exportable = new stdClass();
        $notExportable = new ClassWithPropertyNotExportable([$file]);
        $notExportableClassName = ClassWithPropertyNotExportable::class;
        $exportable->string = 'test';
        $exportable->array = [$notExportable];
        $atBreadcrumb =
            <<<STRING
            Argument contains a resource at `[object: stdClass][property: \$array][(iterable)][key: 0][object: {$notExportableClassName}][property: array \$files][(iterable)][key: 0]`
            STRING;
        $storable = new StorableVariable($exportable);
        $this->expectException(UnableToStoreException::class);
        $this->expectExceptionMessage($atBreadcrumb);
        $storable->toExport();
    }

    public function testContainsNotExportableResource(): void
    {
        $object = new stdClass();
        $resource = fopen(__FILE__, 'r');
        $object->resource = $resource;
        $array = [1, 2, 3, $object];
        $atBreadcrumb =
            <<<STRING
            Argument contains a resource at `[(iterable)][key: 3][object: stdClass][property: \$resource]`
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
            $this->assertSame(VarExporter::export($val), $storableVariable->toExport());
        }
    }
}
