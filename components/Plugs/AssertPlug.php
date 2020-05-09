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

use Chevere\Components\Hooks\Exceptions\HookableInterfaceException;
use Chevere\Components\Hooks\Interfaces\HookableInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Exceptions\PlugableNotExistsException;
use Chevere\Components\Plugs\Exceptions\PlugAnchorNotExistsException;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\PlugableAnchors;
use Error;
use InvalidArgumentException;
use LogicException;

final class AssertPlug
{
    private PlugInterface $plug;

    private PlugTypeInterface $type;

    public function __construct(PlugInterface $plug)
    {
        $this->plug = $plug;
        $this->assertPlugableExists();
        $plugDetect = new PlugDetect($this->plug);
        $anchorsMethod = $plugDetect->type()->plugableAnchorsMethod();
        $at = $this->plug->at();
        try {
            $anchors = $at::$anchorsMethod();
        } catch (Error $e) {
            throw new InvalidArgumentException(
                (new Message('Unsupported plugable %className% declared by plug %plug% %message%'))
                    ->code('%className%', $at)
                    ->code('%plug%', get_class($this->plug))
                    ->code('%message%', $e->getMessage())
                    ->toString()
            );
        }
        $this->assertAnchors($anchors);
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
            throw new PlugableNotExistsException(
                (new Message("Class %ClassName% doesn't exists"))
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }

    private function assertAnchors(PlugableAnchors $anchors): void
    {
        if ($anchors->has($this->plug->for()) === false) {
            throw new PlugAnchorNotExistsException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->plug->for())
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }
}
