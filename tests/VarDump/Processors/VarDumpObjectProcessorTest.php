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

namespace Chevere\Tests\VarDump\Processors;

use Chevere\Tests\VarDump\_resources\DummyClass;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\VarDump\Interfaces\VarDumpProcessorInterface;
use Chevere\VarDump\Processors\VarDumpObjectProcessor;
use Ds\Map;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumpObjectProcessorTest extends TestCase
{
    use VarDumperTrait;

    private function hookTestOutput(string $expected, array $lines): void
    {
        $this->assertSame(implode("\n", $lines), $expected);
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new VarDumpObjectProcessor($this->getVarDumper(null));
    }

    public function testEmptyObject(): void
    {
        $object = new stdClass();
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $this->assertSame(1, $processor->depth());
        $this->assertSame(stdClass::class . '#' . $id, $processor->info());
        $processor->write();
        $this->assertSame(
            stdClass::class . '#' . $id,
            $varDumper->writer()->__toString()
        );
    }

    public function testUnsetObject(): void
    {
        $object = new DummyClass();
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $this->assertSame(DummyClass::class . '#' . $id, $processor->info());
        $processor->write();
        $stringEls = [
            $object::class . '#' . strval(spl_object_id($object)),
            ' public $public null',
            ' private $private null',
            ' private $protected null',
            ' private $circularReference null',
            ' private $deep null',
        ];
        $this->hookTestOutput($varDumper->writer()->__toString(), $stringEls);
    }

    public function testObjectProperty(): void
    {
        $object = (new DummyClass())->withPublic();
        $id = strval(spl_object_id($object));
        $pubId = strval(spl_object_id($object->public));
        $varDumper = $this->getVarDumper($object);
        (new VarDumpObjectProcessor($varDumper))->write();
        $stringEls = [
            $object::class . '#' . $id,
            ' public $public stdClass#' . $pubId,
            ' public $string string string (length=6)',
            ' public $array array (size=0)',
            ' public $int integer 1 (length=1)',
            ' public $bool boolean true',
            ' private $private null',
            ' private $protected null',
            ' private $circularReference null',
            ' private $deep null',
        ];
        $this->hookTestOutput($varDumper->writer()->__toString(), $stringEls);
    }

    public function testAnonClass(): void
    {
        $object = new class() {
        };
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        (new VarDumpObjectProcessor($varDumper))->write();
        $this->assertSame(
            'class@anonymous#' . $id,
            $varDumper->writer()->__toString()
        );
    }

    public function testCircularReference(): void
    {
        $object = (new DummyClass())->withCircularReference();
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $processor->write();
        $stringEls = [
            $object::class . '#' . $id,
            ' public $public null',
            ' private $private null',
            ' private $protected null',
            ' private $circularReference '
                . $object::class
                . '#'
                . $id
                . ' *circular reference* #' . $id,
            ' private $deep null',
        ];
        $this->hookTestOutput($varDumper->writer()->__toString(), $stringEls);
    }

    public function testDeep(): void
    {
        $deep = new stdClass();
        for ($i = 0; $i <= VarDumpProcessorInterface::MAX_DEPTH; $i++) {
            $deep = new class($deep) {
                public function __construct(public $deep)
                {
                }
            };
            $objectIds[] = strval(spl_object_id($deep));
        }
        $objectIds = array_reverse($objectIds);
        $object = (new DummyClass())
            ->withDeep($deep);
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $processor->write();
        $stringEls = [
            $object::class . '#' . $id,
            ' public $public null',
            ' private $private null',
            ' private $protected null',
            ' private $circularReference null',
            ' private $deep class@anonymous#' . $objectIds[0],
            ' public $deep class@anonymous#' . $objectIds[1],
            '  public $deep class@anonymous#' . $objectIds[2],
            '   public $deep class@anonymous#' . $objectIds[3],
            '    public $deep class@anonymous#' . $objectIds[4],
            '     public $deep class@anonymous#' . $objectIds[5],
            '      public $deep class@anonymous#' . $objectIds[6],
            '       public $deep class@anonymous#' . $objectIds[7],
            '        public $deep class@anonymous#' . $objectIds[8],
            '         public $deep class@anonymous#' . $objectIds[9]
                . ' *max depth reached*',
        ];
        $this->hookTestOutput($varDumper->writer()->__toString(), $stringEls);
    }

    public function testDsCollection(): void
    {
        $key = 'key';
        $value = 'value';
        $objectChild = new Map(['test']);
        $object = new Map([$key => $value, 'map' => $objectChild]);
        $id = strval(spl_object_id($object));
        $idChild = strval(spl_object_id($objectChild));
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $processor->write();
        $stringEls = [
            $object::class . '#' . $id . ' array (size=2)',
            'key => string value (length=5)',
            'map => Ds\Map#' . $idChild . ' array (size=1)',
            ' 0 => string test (length=4)',
        ];
        $this->hookTestOutput($varDumper->writer()->__toString(), $stringEls);
    }
}
