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

use Chevere\Components\Events\Interfaces\EventableInterface;
use Chevere\Components\Events\Interfaces\EventListenerInterface;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\Types\EventListenerPlugType;
use Chevere\Components\Plugs\Types\HookPlugType;
use Chevere\Components\Plugs\Types\PlugTypesList;
use PHPUnit\Framework\TestCase;

final class PlugTypesTest extends TestCase
{
    public function testHookPlugType(): void
    {
        $this->plugTypeTester(
            new HookPlugType,
            HookInterface::class,
            HookableInterface::class,
            'Hook.php'
        );
    }

    public function testEventListenerPlugType(): void
    {
        $this->plugTypeTester(
            new EventListenerPlugType,
            EventListenerInterface::class,
            EventableInterface::class,
            'EventListener.php'
        );
    }

    private function plugTypeTester(
        PlugTypeInterface $plugType,
        string $plugInterface,
        string $plugableInterface,
        string $trailingName
    ): void {
        $plugType = $plugType;
        $this->assertSame($plugInterface, $plugType->interface());
        $this->assertSame($plugableInterface, $plugType->plugsTo());
        $this->assertSame($trailingName, $plugType->trailingName());
        $this->assertTrue(method_exists(
            $plugType->plugsTo(),
            $plugType->plugableAnchorsMethod()
        ));
    }
}
