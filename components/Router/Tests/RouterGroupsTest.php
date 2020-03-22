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

namespace Chevere\Tests\Router\Properties;

use BadMethodCallException;
use Chevere\Components\Router\RouterGroups;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouterGroupsTest extends TestCase
{
    public function testEmpty(): void
    {
        $group = 'some-group';
        $routerGroups = new RouterGroups();
        $this->assertSame([], $routerGroups->toArray());
        $this->assertFalse($routerGroups->has($group));
        $this->expectException(OutOfBoundsException::class);
        $routerGroups->get($group);
    }

    public function testWithAdded(): void
    {
        $array = [
            'group-1' => ['name-1', 'name-2'],
            'group-2' => ['name-3'],
        ];
        $routerGroups = new RouterGroups();
        foreach ($array as $group => $ids) {
            foreach ($ids as $id) {
                $routerGroups = $routerGroups->withAdded($group, $id);
            }
            $this->assertTrue($routerGroups->has($group));
            $this->assertSame($ids, $routerGroups->get($group));
        }
    }
}
