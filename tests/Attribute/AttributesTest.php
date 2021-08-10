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

namespace Chevere\Tests\Controller\Attributes;

use Chevere\Components\Attribute\Relation;
use Chevere\Components\Workflow\Attributes\Dispatch;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ControllerAttributesTest extends TestCase
{
    public function testRelation(): void
    {
        $relation = 'relation';
        $attribute = new Relation($relation);
        $this->assertSame($relation, $attribute->attribute());
    }

    public function testDispatch(): void
    {
        $matrix = Dispatch::knownEvents();
        foreach ($matrix as $pos => $event) {
            $attribute = new Dispatch($event);
            $this->assertSame($event, $attribute->attribute());
        }
        $this->expectException(InvalidArgumentException::class);
        new Dispatch('aaaa');
    }
}
