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
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Plugin\PlugInterface;
use Chevere\Interfaces\Plugin\PlugsMapInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Interfaces\Plugin\PlugsQueueTypedInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;
use Ds\Map;
use Ds\Set;
use Generator;
use Throwable;

final class PlugsMap implements PlugsMapInterface
{
    /**
     * @var Set [PlugsQueueInterface,]
     */
    private Set $set;

    /**
     * @var Map [pluggableClassName => PlugsQueueInterface,]
     */
    private Map $map;

    private PlugTypeInterface $type;

    public function __construct(PlugTypeInterface $type)
    {
        $this->set = new Set;
        $this->map = new Map;
        $this->type = $type;
    }

    public function plugType(): PlugTypeInterface
    {
        return $this->type;
    }

    public function withAdded(PlugInterface $plug): PlugsMapInterface
    {
        try {
            $assert = new AssertPlug($plug);
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new InvalidArgumentException(null, 0, $e);
        }
        // @codeCoverageIgnoreEnd
        if (!($assert->plugType() instanceof $this->type)) {
            throw new InvalidArgumentException(
                (new Message('Argument passed must be an instance of type %type%'))
                    ->code('%type%', get_class($this->type))
            );
        }
        $this->assertUnique($plug);
        /**
         * @var PlugsQueueInterface $queue
         */
        $queue = $this->map->hasKey($plug->at())
            ? $this->map->get($plug->at())
            : $assert->plugType()->getPlugsQueueTyped();
        $new = clone $this;
        $new->map[$plug->at()] = $queue->withAdded($plug);
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

    public function hasPlugsFor(string $pluggable): bool
    {
        return $this->map->hasKey($pluggable);
    }

    public function getPlugsQueueTypedFor(string $pluggable): PlugsQueueTypedInterface
    {
        try {
            return $this->map->get($pluggable, new PlugsQueue($this->type));
        }
        // @codeCoverageIgnoreStart
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(null, 0, $e);
        }
        // @codeCoverageIgnoreEnd
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
            throw new OverflowException(
                (new Message('%plug% has been already registered'))
                    ->code('%plug%', get_class($plug))
            );
        }
    }
}
