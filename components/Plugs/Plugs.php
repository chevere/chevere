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

namespace Chevere\Components\Plugs;

use Chevere\Components\Hooks\Exceptions\AnchorNotFoundException;
use Chevere\Components\Hooks\Exceptions\HookableInterfaceException;
use Chevere\Components\Hooks\Exceptions\HookableNotFoundException;
use Chevere\Components\Hooks\HookAnchors;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;

final class Plugs
{
    private PlugInterface $plug;

    public function __construct(PlugInterface $plug)
    {
        $this->plug = $plug;
        $this->assertPlugableExists();
        $this->assertPlugableInterface();
        $this->assertAnchor();
    }

    public function plug(): PlugInterface
    {
        return $this->plug;
    }

    private function assertPlugableExists(): void
    {
        if (class_exists($this->plug->at()) === false) {
            throw new HookableNotFoundException(
                (new Message("Class %ClassName% doesn't exists"))
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }

    private function assertPlugableInterface(): void
    {
        if (is_a($this->plug->at(), HookableInterface::class, true) === false) {
            throw new HookableInterfaceException(
                (new Message('Class %ClassName% must implement the %interfaceName% interface'))
                    ->code('%ClassName%', $this->plug->at())
                    ->code('%interfaceName%', HookableInterface::class)
            );
        }
    }

    private function assertAnchor(): void
    {
        /**
         * @var HookAnchors $anchors
         */
        $anchors = $this->plug->at()::getHookAnchors();
        if ($anchors->has($this->plug->for()) === false) {
            throw new AnchorNotFoundException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->plug->for())
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }
}
