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
use Chevere\Pluggable\Interfaces\PlugInterface;
use Chevere\Pluggable\Interfaces\PlugsMapInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueTypedInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exception;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use Ds\Map;
use Ds\Set;
use Iterator;

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

    public function __construct(
        private PlugTypeInterface $type
    ) {
        $this->set = new Set();
        $this->map = new Map();
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
        catch (Exception $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: (new Message('Invalid argument %argument% provided'))
                    ->code('%argument%', '$plug'),
            );
        }
        // @codeCoverageIgnoreEnd
        if (!($assert->plugType() instanceof $this->type)) {
            throw new InvalidArgumentException(
                (new Message('Argument passed must be an instance of type %type%'))
                    ->code('%type%', $this->type::class)
            );
        }
        $this->assertUnique($plug);
        /** @var PlugsQueueInterface $queue */
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

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function getPlugsQueueTypedFor(string $pluggable): PlugsQueueTypedInterface
    {
        try {
            return $this->map->get($pluggable);
        }
        // @codeCoverageIgnoreStart
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Pluggable %pluggable% not found'))
                    ->code('%pluggable%', $pluggable)
            );
        }
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): Iterator
    {
        foreach ($this->map->pairs() as $pair) {
            yield $pair->key => $pair->value;
        }
    }

    private function assertUnique(PlugInterface $plug): void
    {
        if ($this->has($plug)) {
            throw new OverflowException(
                (new Message('%plug% has been already registered'))
                    ->code('%plug%', $plug::class)
            );
        }
    }
}
