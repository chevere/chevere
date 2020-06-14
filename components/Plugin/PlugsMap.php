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

namespace Chevere\Components\Plugin;

use Chevere\Components\Message\Message;
use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Plugin\PlugRegisteredException;
use Chevere\Interfaces\Plugin\AssertPlugInterface;
use Chevere\Interfaces\Plugin\PlugInterface;
use Chevere\Interfaces\Plugin\PlugsMapInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;
use Ds\Map;
use Ds\Set;
use Generator;

final class PlugsMap implements PlugsMapInterface
{
    private Set $set;

    private Map $map;

    private PlugTypeInterface $type;

    public function __construct(PlugTypeInterface $type)
    {
        $this->set = new Set;
        $this->map = new Map;
        $this->type = $type;
    }

    public function type(): PlugTypeInterface
    {
        return $this->type;
    }

    public function withAdded(AssertPlugInterface $assertPlug): PlugsMapInterface
    {
        if (!($assertPlug->type() instanceof $this->type)) {
            throw new InvalidArgumentException(
                (new Message('Argument passed must be an instance of type %type%'))
                    ->code('%type%', get_class($this->type))
            );
        }
        $plug = $assertPlug->plug();
        $this->assertUnique($plug);
        /**
         * @var PlugsQueueInterface $queue
         */
        $queue = $this->map->hasKey($plug->at())
            ? $this->map->get($plug->at())
            : new PlugsQueue($assertPlug->type());
        $new = clone $this;
        $new->map[$plug->at()] = $queue->withAddedPlug($plug);
        $new->set->add($plug);

        return $new;
    }

    public function count(): int
    {
        return $this->set->count();
    }

    public function has(PlugInterface $plug): bool
    {
        return $this->set->contains($plug);
    }

    public function hasPlugsQueueFor(string $pluggable): bool
    {
        return $this->map->hasKey($pluggable);
    }

    public function getPlugQueueFor(string $pluggable): PlugsQueueInterface
    {
        return $this->map->get($pluggable);
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
