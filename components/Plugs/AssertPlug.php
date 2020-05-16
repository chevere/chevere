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

use Chevere\Components\Message\Message;
use Chevere\Components\Plugs\Exceptions\PluggableAnchorNotExistsException;
use Chevere\Components\Plugs\Exceptions\PluggableAnchorsException;
use Chevere\Components\Plugs\Exceptions\PluggableNotExistsException;
use Chevere\Components\Plugs\Exceptions\PlugInterfaceException;
use Chevere\Components\Plugs\Interfaces\AssertPlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\PluggableAnchors;
use Chevere\Components\Plugs\PlugTypesList;
use Error;

/**
 * Asserts everything about a plug
 */
final class AssertPlug implements AssertPlugInterface
{
    /** @var PlugTypesList PlugTypeInterface[] */
    private PlugTypesList $plugTypesList;

    private PlugInterface $plug;

    private PlugTypeInterface $type;

    public function __construct(PlugInterface $plug)
    {
        $this->plugTypesList = new PlugTypesList;
        $this->plug = $plug;
        /**
         * @var PlugTypeInterface $plugType
         */
        foreach ($this->plugTypesList->getGenerator() as $plugType) {
            $plugInterface = $plugType->interface();
            if ($this->plug instanceof $plugInterface) {
                $this->type = $plugType;
                break;
            }
        }
        $this->assertType();
        $this->assertPluggableExists();
        $anchorsMethod = $this->type()->pluggableAnchorsMethod();
        $at = $this->plug->at();
        try {
            $anchors = $at::$anchorsMethod();
        } catch (Error $e) {
            throw new PluggableAnchorsException(
                (new Message('Unable to retrieve %className% pluggable anchors declared by plug %plug% %message%'))
                    ->code('%className%', $at)
                    ->code('%plug%', get_class($this->plug))
                    ->code('%message%', $e->getMessage())
            );
        }
        $this->assertAnchors($anchors);
    }

    public function type(): PlugTypeInterface
    {
        return $this->type;
    }

    public function plug(): PlugInterface
    {
        return $this->plug;
    }

    private function assertType(): void
    {
        if (!isset($this->type)) {
            $accept = [];
            /**
             * @var PlugTypeInterface $plugType
             */
            foreach ($this->plugTypesList->getGenerator() as $plugType) {
                $accept[] = $plugType->interface();
            }
            throw new PlugInterfaceException(
                (new Message("Plug %className% doesn't implement any of the accepted plug interfaces %interfaces%"))
                    ->code('%className%', $this->plug->at())
                    ->code('%interfaces%', implode(',', $accept))
            );
        }
    }

    private function assertPluggableExists(): void
    {
        if (class_exists($this->plug->at()) === false) {
            throw new PluggableNotExistsException(
                (new Message("Class %ClassName% doesn't exists"))
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }

    private function assertAnchors(PluggableAnchors $anchors): void
    {
        if ($anchors->has($this->plug->anchor()) === false) {
            throw new PluggableAnchorNotExistsException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->plug->anchor())
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }
}
