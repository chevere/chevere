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

namespace Chevere\Tests\Spec\Specs;

use function Chevere\Filesystem\dirForPath;
use Chevere\Spec\Specs\GroupSpec;
use Chevere\Spec\Specs\GroupSpecs;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class GroupsSpecsTest extends TestCase
{
    public function testConstruct(): void
    {
        $spec = new GroupSpecs();
        $key = 'key';
        $this->assertCount(0, $spec);
        $this->assertFalse($spec->has($key));
        $this->expectException(OutOfBoundsException::class);
        $spec->get($key);
    }

    public function testWithPut(): void
    {
        $specs = new GroupSpecs();
        $spec = new GroupSpec(
            dirForPath('/spec/'),
            'repo'
        );
        $specs = $specs->withPut($spec);
        $this->assertCount(1, $specs);
        $this->assertTrue($specs->has($spec->key()));
        $this->assertSame($spec, $specs->get($spec->key()));
    }
}
