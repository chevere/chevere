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

namespace Chevere\Components\Plugin\Tests;

use Chevere\Components\Plugin\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugin\Types\EventListenerPlugType;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Components\Plugs\EventListener\Interfaces\EventListenerInterface;
use Chevere\Components\Plugs\EventListener\Interfaces\PluggableEventsInterface;
use Chevere\Components\Plugs\Hooks\Interfaces\HookInterface;
use Chevere\Components\Plugs\Hooks\Interfaces\PluggableHooksInterface;
use PHPUnit\Framework\TestCase;

final class PlugTypesTest extends TestCase
{
    public function testHookPlugType(): void
    {
        $this->plugTypeTester(
            new HookPlugType,
            HookInterface::class,
            PluggableHooksInterface::class,
            'Hook.php',
            'Hooks'
        );
    }

    public function testEventListenerPlugType(): void
    {
        $this->plugTypeTester(
            new EventListenerPlugType,
            EventListenerInterface::class,
            PluggableEventsInterface::class,
            'EventListener.php',
            'EventListeners'
        );
    }

    private function plugTypeTester(
        PlugTypeInterface $plugType,
        string $plugInterface,
        string $pluggableInterface,
        string $trailingName,
        string $queueName
    ): void {
        $this->assertSame($plugInterface, $plugType->interface());
        $this->assertSame($pluggableInterface, $plugType->plugsTo());
        $this->assertSame($trailingName, $plugType->trailingName());
        $this->assertSame($queueName, $plugType->queueName());
        $this->assertTrue(method_exists(
            $plugType->plugsTo(),
            $plugType->pluggableAnchorsMethod()
        ), $plugType->plugsTo() . '::' . $plugType->pluggableAnchorsMethod());
    }
}
