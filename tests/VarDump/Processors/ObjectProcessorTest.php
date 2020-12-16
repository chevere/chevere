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

use Chevere\Components\VarDump\Processors\VarDumpObjectProcessor;
use Chevere\Interfaces\VarDump\VarDumpProcessorInterface;
use Chevere\Tests\VarDump\Traits\VarDumperTrait;
use Ds\Map;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testEmptyObject(): void
    {
        $object = new stdClass();
        $id = (string) spl_object_id($object);
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $this->assertSame(stdClass::class . '#' . $id, $processor->info());
        $processor->write();
        $this->assertSame(stdClass::class . '#' . $id, $varDumper->writer()->toString());
    }

    public function testUnsetObject(): void
    {
        $object = new DummyClass();
        $id = (string) spl_object_id($object);
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $this->assertSame(DummyClass::class . '#' . $id, $processor->info());
        $processor->write();
        $string = $varDumper->writer()->toString();
        $this->assertStringContainsString(
            'public $public null',
            $string
        );
        $this->assertStringContainsString(
            'private $protected null',
            $string
        );
        $this->assertStringContainsString(
            'private $private null',
            $string
        );
        $this->assertStringContainsString(
            'private $circularReference null',
            $string
        );
    }

    public function testObjectProperty(): void
    {
        $varDumper = $this->getVarDumper((new DummyClass())->withPublic());
        (new VarDumpObjectProcessor($varDumper))->write();
        $this->assertStringContainsString(
            'public $public stdClass',
            $varDumper->writer()->toString()
        );
    }

    public function testAnonClass(): void
    {
        $object = new class() {
        };
        $id = (string) spl_object_id($object);
        $varDumper = $this->getVarDumper($object);
        (new VarDumpObjectProcessor($varDumper))->write();
        $this->assertSame('class@anonymous#' . $id, $varDumper->writer()->toString());
    }

    public function testCircularReference(): void
    {
        $object = (new DummyClass())->withCircularReference();
        $id = (string) spl_object_id($object);
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $processor->write();
        $this->assertStringContainsString(
            'private $circularReference ' . DummyClass::class . '#' . $id . ' ' . $processor->circularReference(),
            $varDumper->writer()->toString()
        );
    }

    public function testDeep(): void
    {
        $object = (new DummyClass())->withDeep(VarDumpProcessorInterface::MAX_DEPTH);
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $processor->write();
        $this->assertStringContainsString(
            $processor->maxDepthReached(),
            $varDumper->writer()->toString()
        );
    }

    public function testDsCollection(): void
    {
        $key = 'key';
        $value = 'value';
        $object = new Map([$key => $value]);
        $varDumper = $this->getVarDumper($object);
        $processor = new VarDumpObjectProcessor($varDumper);
        $processor->write();
        $this->assertStringContainsString(
            "$key => string $value",
            $varDumper->writer()->toString()
        );
    }
}

final class DummyClass
{
    private object $private;

    private object $protected;

    public object $public;

    private object $circularReference;

    private object $deep;

    public function withPrivate(): self
    {
        $new = clone $this;
        $new->private = new stdClass();

        return $new;
    }

    public function withProtected(): self
    {
        $new = clone $this;
        $new->protected = new stdClass();

        return $new;
    }

    public function withPublic(): self
    {
        $new = clone $this;
        $new->public = new stdClass();
        $new->public->string = 'string';
        $new->public->array = [];
        $new->public->int = 1;
        $new->public->bool = true;

        return $new;
    }

    public function withCircularReference(): self
    {
        $new = clone $this;
        $new->circularReference = $new;

        return $new;
    }

    public function withDeep(int $deep): self
    {
        $new = clone $this;
        $array = ['deep' => []];
        for ($i = 0; $i <= $deep; $i++) {
            $array = ['deep' => $array];
        }
        $object = json_encode($array);
        $new->deep = json_decode($object);

        return $new;
    }
}
