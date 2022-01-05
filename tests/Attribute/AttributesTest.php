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

namespace Chevere\Tests\Attribute;

use Chevere\Components\Attribute\Dispatch;
use Chevere\Components\Attribute\Relation;
use PHPUnit\Framework\TestCase;

final class AttributesTest extends TestCase
{
    public function testRelation(): void
    {
        $relation = 'relation';
        $attribute = new Relation($relation);
        $this->assertSame($relation, $attribute->attribute());
    }

    public function testDispatch(): void
    {
        $dispatch = 'dispatch';
        $attribute = new Dispatch($dispatch);
        $this->assertSame($dispatch, $attribute->attribute());
    }
}
