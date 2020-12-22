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

use Chevere\Components\Pluggable\Types\EventPlugType;
use Chevere\Components\Pluggable\Types\HookPlugType;
use Chevere\Interfaces\Pluggable\Plug\Event\EventInterface;
use Chevere\Interfaces\Pluggable\Plug\Event\EventQueueInterface;
use Chevere\Interfaces\Pluggable\Plug\Event\PluggableEventsInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\HookInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\HooksQueueInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\PluggableHooksInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;
use PHPUnit\Framework\TestCase;

final class PlugTypesTest extends TestCase
{
    public function testHookPlugType(): void
    {
        $this->plugTypeTester(
            new HookPlugType(),
            HookInterface::class,
            PluggableHooksInterface::class,
            HooksQueueInterface::class,
            'Hook.php'
        );
    }

    public function testEventPlugType(): void
    {
        $this->plugTypeTester(
            new EventPlugType(),
            EventInterface::class,
            PluggableEventsInterface::class,
            EventQueueInterface::class,
            'Event.php',
        );
    }

    private function plugTypeTester(
        PlugTypeInterface $plugType,
        string $plugInterface,
        string $pluggableInterface,
        string $plugQueueTypedInterface,
        string $trailingName
    ): void {
        $this->assertSame($plugInterface, $plugType->interface());
        $this->assertSame($pluggableInterface, $plugType->plugsTo());
        $this->assertSame($trailingName, $plugType->trailingName());
        $this->assertInstanceOf($plugQueueTypedInterface, $plugType->getPlugsQueueTyped());
        $this->assertTrue(method_exists(
            $plugType->plugsTo(),
            $plugType->pluggableAnchorsMethod()
        ), $plugType->plugsTo() . '::' . $plugType->pluggableAnchorsMethod());
    }
}
