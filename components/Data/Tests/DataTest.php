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

namespace Chevere\Components\Data\Tests;

use Chevere\Components\Data\Data;
use PHPUnit\Framework\TestCase;

final class DataTest extends TestCase
{
    public function getMixedArray(): array
    {
        return [1, 2, 3, 'key' => 'value'];
    }

    public function getBasicArray(): array
    {
        return [1, 2, 3];
    }

    public function testConstructEmpty(): void
    {
        $array = [];
        $data = new Data($array);
        $this->assertSame($array, $data->toArray());
        $this->assertTrue($data->isEmpty());
        $this->assertFalse($data->hasKey(''));
        $this->assertSame(0, $data->count());
    }

    public function testConstruct(): void
    {
        $mixed = $this->getMixedArray();
        $data = new Data($mixed);
        $this->assertSame($mixed, $data->toArray());
        $this->assertFalse($data->isEmpty());
        $this->assertTrue($data->hasKey('key'));
        $this->assertSame($mixed['key'], $data->key('key'));
        $this->assertSame(count($mixed), $data->count());
    }

    public function testWithArray(): void
    {
        $array = $this->getBasicArray();
        $mixed = $this->getMixedArray();
        $data = (new Data($array))
            ->withArray($mixed);
        $this->assertSame($mixed, $data->toArray());
    }

    public function testWithMergedArray(): void
    {
        $array = $this->getBasicArray();
        $mixed = $this->getMixedArray();
        $merge = array_merge_recursive($mixed, $array);
        $data = (new Data($mixed))
            ->withMergedArray($array);
        $this->assertSame($merge, $data->toArray());
    }

    public function testWithAppend(): void
    {
        $array = $this->getBasicArray();
        $data = (new Data($array))
            ->withAppend('var');
        $expected = $array;
        $expected[] = 'var';
        $this->assertSame($expected, $data->toArray());
        $this->assertSame(count($array), array_key_last($data->toArray()));
    }

    public function testWithAddedKey(): void
    {
        $value = ['value', 1];
        $array = $this->getMixedArray();
        $data = (new Data($array))
            ->withAddedKey('test', $value);
        $this->assertTrue($data->hasKey('test'));
        $this->assertSame($value, $data->key('test'));
    }

    public function testWithRemovedkey(): void
    {
        $array = $this->getMixedArray();
        $data = new Data($array);
        $this->assertTrue($data->hasKey('key'));
        $data = $data
            ->withRemovedKey('key');
        $this->assertFalse($data->hasKey('key'));
    }
}
