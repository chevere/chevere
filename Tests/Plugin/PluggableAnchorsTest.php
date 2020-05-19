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

namespace Chevere\Tests\Plugin;

use Chevere\Exceptions\Plugin\PluggableAnchorExistsException;
use Chevere\Components\Plugin\PluggableAnchors;
use PHPUnit\Framework\TestCase;

final class PluggableAnchorsTest extends TestCase
{
    public function testConstruct(): void
    {
        $pluggableAnchors = new PluggableAnchors;
        $this->assertCount(0, $pluggableAnchors->set());
        $this->assertFalse($pluggableAnchors->has('anchor'));
    }

    public function testWithAddedAnchor(): void
    {
        $anchor = 'anchor';
        $pluggableAnchors = (new PluggableAnchors)
            ->withAddedAnchor($anchor);
        $this->assertCount(1, $pluggableAnchors->set());
        $this->assertTrue($pluggableAnchors->has($anchor));
        $this->expectException(PluggableAnchorExistsException::class);
        $pluggableAnchors->withAddedAnchor($anchor);
    }
}
