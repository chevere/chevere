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
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\PlugsQueue;
use Ds\Map;
use Ds\Set;
use LogicException;
use function DeepCopy\deep_copy;

final class PlugsMapper
{
    protected Set $set;

    protected Map $map;

    public function __construct()
    {
        $this->set = new Set;
        $this->map = new Map;
    }

    public function withAddedPlug(AssertPlug $assertPlug): self
    {
        $plug = $assertPlug->plug();
        $this->assertUnique($plug);
        $queue = $this->map->hasKey($plug->at())
            ? $this->map->get($plug->at())
            : new PlugsQueue;
        $this->map[$plug->at()] = $queue->withAddedPlug($plug);

        return $this;
    }

    protected function assertUnique(PlugInterface $plug): void
    {
        $plugName = get_class($plug);
        if ($this->set->contains($plugName)) {
            throw new LogicException(
                (new Message('%plugName% has been already registered'))
                    ->code('%plugName%', $plugName)
                    ->toString()
            );
        }
    }

    public function map(): Map
    {
        return deep_copy($this->map);
    }
}
