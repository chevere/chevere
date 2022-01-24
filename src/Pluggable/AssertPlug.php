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

namespace Chevere\Pluggable;

use Chevere\Message\Message;
use Chevere\Pluggable\Interfaces\AssertPlugInterface;
use Chevere\Pluggable\Interfaces\PlugInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\ClassNotExistsException;
use Chevere\Throwable\Exceptions\LogicException;
use Error;

final class AssertPlug implements AssertPlugInterface
{
    /**
     * @var PlugTypesList PlugTypeInterface[]
     */
    private PlugTypesList $plugTypesList;

    private PlugTypeInterface $plugType;

    public function __construct(
        private PlugInterface $plug
    ) {
        $this->plugTypesList = new PlugTypesList();
        foreach ($this->plugTypesList->getIterator() as $plugType) {
            $plugInterface = $plugType->interface();
            if ($this->plug instanceof $plugInterface) {
                $this->plugType = $plugType;

                break;
            }
        }
        $this->assertType();
        $this->assertPluggableExists();
        $anchorsMethod = $this->plugType()->pluggableAnchorsMethod();
        $at = $this->plug->at();

        try {
            $anchors = $at::$anchorsMethod();
        } catch (Error $e) {
            throw new LogicException(
                (new Message('Unable to retrieve %className% pluggable anchors declared by plug %plug% %message%'))
                    ->code('%className%', $at)
                    ->code('%plug%', $this->plug::class)
                    ->code('%message%', $e->getMessage())
            );
        }
        $this->assertAnchors($anchors);
    }

    public function plugType(): PlugTypeInterface
    {
        return $this->plugType;
    }

    public function plug(): PlugInterface
    {
        return $this->plug;
    }

    private function assertType(): void
    {
        if (!isset($this->plugType)) {
            $accept = [];
            /**
             * @var PlugTypeInterface $plugType
             */
            foreach ($this->plugTypesList->getIterator() as $plugType) {
                $accept[] = $plugType->interface();
            }

            throw new TypeError(
                (new Message("Plug %className% doesn't implement any of the accepted plug interfaces %interfaces%"))
                    ->code('%className%', $this->plug->at())
                    ->code('%interfaces%', implode(',', $accept))
            );
        }
    }

    private function assertPluggableExists(): void
    {
        if (!class_exists($this->plug->at())) {
            throw new ClassNotExistsException(
                (new Message("Class %ClassName% doesn't exists"))
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }

    private function assertAnchors(PluggableAnchors $anchors): void
    {
        if (!$anchors->has($this->plug->anchor())) {
            throw new LogicException(
                (new Message('Anchor %anchor% is not declared by %ClassName%'))
                    ->code('%anchor%', $this->plug->anchor())
                    ->code('%ClassName%', $this->plug->at())
            );
        }
    }
}
