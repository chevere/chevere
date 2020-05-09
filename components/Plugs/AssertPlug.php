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
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\PlugableAnchors;
use LogicException;

final class AssertPlug
{
    private PlugInterface $plug;

    private PlugTypeInterface $type;

    public function __construct(PlugInterface $plug)
    {
        $this->plug = $plug;
        $this->assertPlugableExists();
        $this->assertPlugableInterface();
        $plugDetect = new PlugDetect($plug);
        $anchorsMethod = $plugDetect->type()->plugableAnchorsMethod();
        $at = $plug->at();
        $anchors = $at::$anchorsMethod();
        if (isset($anchors)) {
            $this->assertAnchors($anchors);
        } else {
            throw new LogicException(
                (new Message('Unsupported unknown plugable %className%'))
                    ->code('%className%', $at)
                    ->toString()
            );
        }
        $this->type = $plugDetect->type();
    }

    public function type(): PlugTypeInterface
    {
        return $this->type;
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

    private function assertAnchors(PlugableAnchors $anchors): void
    {
        if ($anchors->has($this->plug->for()) === false) {
            throw new AnchorNotFoundException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->plug->for())
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }
}
