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

namespace Chevere\Tests\Pluggable;

use Chevere\Pluggable\PluggableAnchors;
use Chevere\Throwable\Exceptions\OverflowException;
use PHPUnit\Framework\TestCase;

final class PluggableAnchorsTest extends TestCase
{
    public function testConstruct(): void
    {
        $pluggableAnchors = new PluggableAnchors();
        $this->assertCount(0, $pluggableAnchors->clonedSet());
        $this->assertFalse($pluggableAnchors->has('anchor'));
    }

    public function testWithAddedAnchor(): void
    {
        $anchor = 'anchor';
        $pluggableAnchors = new PluggableAnchors();
        $pluggableAnchorsWithAdded = $pluggableAnchors
            ->withAdded($anchor);
        $this->assertCount(1, $pluggableAnchorsWithAdded->clonedSet());
        $this->assertTrue($pluggableAnchorsWithAdded->has($anchor));
        $this->expectException(OverflowException::class);
        $pluggableAnchorsWithAdded->withAdded($anchor);
    }
}
