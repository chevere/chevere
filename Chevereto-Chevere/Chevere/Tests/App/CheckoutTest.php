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

use InvalidArgumentException;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Checkout;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Response;
use PHPUnit\Framework\TestCase;

final class CheckoutTest extends TestCase
{
    public function testConstructWithNotMakedBuild(): void
    {
        $services = new Services();
        $response = new Response();
        $build = new Build(new App($services, $response));
        $this->assertFalse($build->isMaked());
        $this->expectException(InvalidArgumentException::class);
        new Checkout($build);
    }
}
