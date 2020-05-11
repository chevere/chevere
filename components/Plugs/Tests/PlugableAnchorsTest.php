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

namespace Chevere\Components\Plugs\Tests;

use Chevere\Components\Plugs\Exceptions\PlugableAnchorExistsException;
use Chevere\Components\Plugs\PlugableAnchors;
use PHPUnit\Framework\TestCase;

final class PlugableAnchorsTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugableAnchors = new PlugableAnchors;
        $this->assertCount(0, $plugableAnchors->set());
        $this->assertFalse($plugableAnchors->has('anchor'));
    }

    public function testWithAddedAnchor(): void
    {
        $anchor = 'anchor';
        $plugableAnchors = (new PlugableAnchors)
            ->withAddedAnchor($anchor);
        $this->assertCount(1, $plugableAnchors->set());
        $this->assertTrue($plugableAnchors->has($anchor));
        $this->expectException(PlugableAnchorExistsException::class);
        $plugableAnchors->withAddedAnchor($anchor);
    }
}
