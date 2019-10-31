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

namespace Chevere\Tests\App;

use Chevere\Components\App\Build;
use Chevere\Components\App\Checkout;
use Chevere\Components\App\Services;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CheckoutTest extends TestCase
{
    public function testConstructWithNotMakedBuild(): void
    {
        $build = new Build(new Services());
        $this->assertFalse($build->isMaked());
        $this->expectException(InvalidArgumentException::class);
        new Checkout($build);
    }
}
