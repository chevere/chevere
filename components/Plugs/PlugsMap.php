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
use Chevere\Components\Plugs\Exceptions\PlugRegisteredException;
use Chevere\Components\Plugs\Interfaces\AssertPlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugInterface;
use Chevere\Components\Plugs\Interfaces\PlugsMapInterface;
use Chevere\Components\Plugs\PlugsQueue;
use Ds\Map;
use Ds\Set;
use Generator;

final class PlugsMap implements PlugsMapInterface
{
    protected Set $set;

    protected Map $map;

    public function __construct()
    {
        $this->set = new Set;
        $this->map = new Map;
    }

    public function withAddedPlug(AssertPlugInterface $assertPlug): PlugsMapInterface
    {
        $plug = $assertPlug->plug();
        $this->assertUnique($plug);
        $queue = $this->map->hasKey($plug->at())
            ? $this->map->get($plug->at())
            : new PlugsQueue($assertPlug->type());
        $new = clone $this;
        $new->map[$plug->at()] = $queue->withAddedPlug($plug);
        $new->set->add(get_class($plug));

        return $new;
    }

    public function count(): int
    {
        return $this->set->count();
    }

    public function has(PlugInterface $plug): bool
    {
        return $this->set->contains(get_class($plug));
    }

    public function hasPlugableName(string $plugableName): bool
    {
        return $this->map->hasKey($plugableName);
    }

    public function getGenerator(): Generator
    {
        foreach ($this->map->pairs() as $pair) {
            yield $pair->key => $pair->value;
        }
    }

    protected function assertUnique(PlugInterface $plug): void
    {
        if ($this->has($plug)) {
            throw new PlugRegisteredException(
                (new Message('%plug% has been already registered'))
                    ->code('%plug%', get_class($plug))
            );
        }
    }
}
