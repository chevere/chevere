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

namespace Chevere\Tests\DataStructure\Traits;

use Chevere\Tests\DataStructure\_resources\UsesVectorTrait;
use PHPUnit\Framework\TestCase;

final class VectorTraitTest extends TestCase
{
    public function testEmpty(): void
    {
        $vector = new UsesVectorTrait();
        $this->assertSame(0, $vector->count());
        $this->assertSame([], $vector->keys());
        $array = iterator_to_array($vector->getIterator());
        $this->assertSame([], $array);
        $cloned = clone $vector;
        $this->assertNotSame($vector, $cloned);
        $this->assertEquals($vector, $cloned);
    }
}
