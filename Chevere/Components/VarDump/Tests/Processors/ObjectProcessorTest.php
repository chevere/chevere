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

use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Tests\AbstractProcessorTest;
use stdClass;

final class ObjectProcessorTest extends AbstractProcessorTest
{
    protected function getProcessorName(): string
    {
        return ObjectProcessor::class;
    }

    protected function getInvalidConstructArgument()
    {
        return [];
    }

    public function testEmptyObject(): void
    {
        $processor = new ObjectProcessor($this->getVarFormat(new stdClass));
        $this->assertSame(stdClass::class, $processor->info());
        $this->assertSame('', $processor->value());
    }

    public function testUnsetObject(): void
    {
        $processor = new ObjectProcessor($this->getVarFormat(new DummyClass));
        $this->assertSame(DummyClass::class, $processor->info());
        $this->assertStringContainsString('public $public null', $processor->value());
        $this->assertStringContainsString('protected $protected null', $processor->value());
        $this->assertStringContainsString('private $private null', $processor->value());
        $this->assertStringContainsString('private $circularReference null', $processor->value());
    }

    public function testObjectProperty(): void
    {
        $processor = new ObjectProcessor($this->getVarFormat((new DummyClass)->withPublic()));
        $this->assertStringContainsString('public $public object stdClass', $processor->value());
    }

    public function testCircularReference(): void
    {
        $object = (new DummyClass)->withCircularReference();
        $processor = new ObjectProcessor($this->getVarFormat($object));
        $this->assertStringContainsString('private $circularReference object ' . DummyClass::class . ' ' . $processor->circularReference(), $processor->value());
    }

    public function testDeep(): void
    {
        $object = (new DummyClass)->withDeep();
        $processor = new ObjectProcessor($this->getVarFormat($object));
        $this->assertStringContainsString('public $deep object stdClass ' . $processor->maxDepthReached(), $processor->value());
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

    public function withDeep(): self
    {
        $new = clone $this;
        $new->deep = new stdClass;
        $new->deep->deep = new stdClass;
        $new->deep->deep->deep = new stdClass;
        $new->deep->deep->deep->deep = new stdClass;
        $new->deep->deep->deep->deep->deep = new stdClass;

        return $new;
    }
}
