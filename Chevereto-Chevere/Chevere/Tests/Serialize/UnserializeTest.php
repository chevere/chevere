<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Serialize;

use Chevere\Components\Serialize\Serialize;
use Chevere\Components\Serialize\Unserialize;
use PHPUnit\Framework\TestCase;

final class UnserializeTest extends TestCase
{
    public function testConstruct(): void
    {
        $object = clone $this;
        $serialized = new Serialize($object);
        $unserialize = new Unserialize($serialized->toString());
        $this->assertEquals(__CLASS__, $unserialize->type()->typeHinting());
        $this->assertInstanceOf(__CLASS__, $unserialize->var());
    }
}
