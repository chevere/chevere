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

namespace Chevere\Components\Extend;

use Chevere\Components\Hooks\Exceptions\AnchorNotFoundException;
use Chevere\Components\Hooks\Exceptions\HookableInterfaceException;
use Chevere\Components\Hooks\Exceptions\HookableNotFoundException;
use Chevere\Components\Hooks\HookAnchors;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Message\Message;

final class AssertPlugin
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
        if (class_exists($this->hook->at()) === false) {
            throw new HookableNotFoundException(
                (new Message("Class %ClassName% doesn't exists"))
                    ->code('%ClassName%', $this->hook->at())
            );
        }
    }

    private function assertHookableInterface(): void
    {
        if (is_a($this->hook->at(), HookableInterface::class, true) === false) {
            throw new HookableInterfaceException(
                (new Message('Class %ClassName% must implement the %interfaceName% interface'))
                    ->code('%ClassName%', $this->hook->at())
                    ->code('%interfaceName%', HookableInterface::class)
            );
        }
    }

    private function assertAnchor(): void
    {
        /**
         * @var HookAnchors $anchors
         */
        $anchors = $this->hook->at()::getHookAnchors();
        if ($anchors->has($this->hook->for()) === false) {
            throw new AnchorNotFoundException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->hook->for())
                    ->code('%ClassName%', $this->hook->at())
            );
        }
    }
}
