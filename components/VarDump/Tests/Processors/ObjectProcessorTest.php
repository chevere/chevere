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

namespace Chevere\Components\VarDump\Tests\Processors;

use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Tests\Traits\VarDumperTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testEmptyObject(): void
    {
        $varDumper = $this->getVarDumper(new stdClass);
        $processor = new ObjectProcessor($varDumper);
        $this->assertSame(stdClass::class, $processor->info());
        $processor->write();
        $this->assertSame(stdClass::class, $varDumper->writer()->toString());
    }

    public function testUnsetObject(): void
    {
        $varDumper = $this->getVarDumper(new DummyClass);
        $processor = new ObjectProcessor($varDumper);
        $this->assertSame(DummyClass::class, $processor->info());
        $processor->write();
        $string = $varDumper->writer()->toString();
        $this->assertStringContainsString(
            'public $public null',
            $string
        );
        $this->assertStringContainsString(
            'protected $protected null',
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
        $varDumper = $this->getVarDumper((new DummyClass)->withPublic());
        (new ObjectProcessor($varDumper))->write();
        $this->assertStringContainsString(
            'public $public stdClass',
            $varDumper->writer()->toString()
        );
    }

    public function testAnonClass(): void
    {
        $object = new class() {
        };
        $varDumper = $this->getVarDumper($object);
        (new ObjectProcessor($varDumper))->write();
        $this->assertSame('class@anonymous', $varDumper->writer()->toString());
    }

    public function testCircularReference(): void
    {
        $object = (new DummyClass)->withCircularReference();
        $varDumper = $this->getVarDumper($object);
        $processor = new ObjectProcessor($varDumper);
        $processor->write();
        $this->assertStringContainsString(
            'private $circularReference ' . DummyClass::class . ' ' . $processor->circularReference(),
            $varDumper->writer()->toString()
        );
    }

    public function testDeep(): void
    {
        $object = (new DummyClass)->withDeep(ProcessorInterface::MAX_DEPTH);
        $varDumper = $this->getVarDumper($object);
        $processor = new ObjectProcessor($varDumper);
        $processor->write();
        $this->assertStringContainsString(
            'public $deep stdClass ' . $processor->maxDepthReached(),
            $varDumper->writer()->toString()
        );
    }
}

final class DummyClass
{
    private object $private;

    protected object $protected;

    public object $public;

    private object $circularReference;

    private object $deep;

    public function withPrivate(): self
    {
        $new = clone $this;
        $new->private = new stdClass;

        return $new;
    }

    public function withProtected(): self
    {
        $new = clone $this;
        $new->protected = new stdClass;

        return $new;
    }

    public function withPublic(): self
    {
        $new = clone $this;
        $new->public = new stdClass;
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
