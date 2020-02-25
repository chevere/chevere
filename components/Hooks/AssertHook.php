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

namespace Chevere\Components\Hooks;

use Chevere\Components\Hooks\Exceptions\AnchorNotFoundException;
use Chevere\Components\Hooks\Exceptions\AssertHookException;
use Chevere\Components\Hooks\Exceptions\HookableInterfaceException;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Hooks\Exceptions\HookableNotFoundException;

final class AssertHook
{
    private HookInterface $hook;

    public function __construct(HookInterface $hook)
    {
        $this->hook = $hook;
        $this->assertHookableExists();
        $this->assertHookableInterface();
        $this->assertAnchor();
    }

    public function hook(): HookInterface
    {
        return $this->hook;
    }

    private function assertHookableExists(): void
    {
        if (class_exists($this->hook::hookableClassname()) === false) {
            throw new HookableNotFoundException(
                (new Message("Class %ClassName% doesn't exists"))
                    ->code('%ClassName%', $this->hook::hookableClassname())
                    ->toString()
            );
        }
    }

    private function assertHookableInterface(): void
    {
        if (is_a($this->hook::hookableClassname(), HookableInterface::class, true) === false) {
            throw new HookableInterfaceException(
                (new Message('Class %ClassName% must implement the %interfaceName% interface'))
                    ->code('%ClassName%', $this->hook::hookableClassname())
                    ->code('%interfaceName%', HookableInterface::class)
                    ->toString()
            );
        }
    }

    private function assertAnchor(): void
    {
        if (!in_array($this->hook::anchor(), $this->hook::hookableClassname()::anchors())) {
            throw new AnchorNotFoundException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->hook::anchor())
                    ->code('%ClassName%', $this->hook::anchor())
                    ->toString()
            );
        }
    }
}
