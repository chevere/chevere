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

namespace Chevere\Tests\Serialize;

use Chevere\Serialize\Deserialize;
use Chevere\Tests\Serialize\_resources\TestUnserializeException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class DeserializeTest extends TestCase
{
    public function getStdClass(): object
    {
        $object = new stdClass();
        $object->prop1 = 'one';
        $object->prop2 = ['two', 3, false];

        return $object;
    }

    public function testConstructUnserializeException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Deserialize('84()/*');
    }

    public function testThrowableUnserializeHandler(): void
    {
        $object = new TestUnserializeException();
        $serialize = serialize($object);
        $this->expectException(InvalidArgumentException::class);
        new Deserialize($serialize);
    }

    public function testConstruct(): void
    {
        $object = $this->getStdClass();
        $objectClass = $object::class;
        $serialized = serialize($object);
        $unserialize = new Deserialize($serialized);
        $this->assertSame($objectClass, $unserialize->type()->typeHinting());
        $this->assertEqualsCanonicalizing($object, $unserialize->var());
        $this->assertInstanceOf($objectClass, $unserialize->var());
    }
}
