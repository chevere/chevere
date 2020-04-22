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

namespace Chevere\Components\Spec\Tests\Specs;

use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\GroupSpec;
use Chevere\Components\Spec\Specs\GroupSpecs;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class GroupsSpecsTest extends TestCase
{
    public function testConstruct(): void
    {
        $spec = new GroupSpecs;
        $key = 'key';
        $this->assertCount(0, $spec->map());
        $this->assertFalse($spec->hasKey($key));
        $this->expectException(OutOfBoundsException::class);
        $spec->get($key);
    }

    public function testWithPut(): void
    {
        $specs = new GroupSpecs;
        $spec = new GroupSpec(
            new SpecPath('/spec'),
            'group-name'
        );
        $specs->put($spec);
        $this->assertCount(1, $specs->map());
        $this->assertTrue($specs->hasKey($spec->key()));
        $this->assertSame($spec, $specs->get($spec->key()));
    }
}
