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

namespace Chevere\Components\Pluggable;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Pluggable\PlugInterface;
use Chevere\Interfaces\Pluggable\PlugsMapInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueTypedInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;
use Ds\Map;
use Ds\Set;
use Generator;
use TypeError;

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
        if (! ($assert->plugType() instanceof $this->type)) {
            throw new InvalidArgumentException(
                (new Message('Argument passed must be an instance of type %type%'))
                    ->code('%type%', get_class($this->type))
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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function getPlugsQueueTypedFor(string $pluggable): PlugsQueueTypedInterface
    {
        try {
            return $this->map->get($pluggable);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Pluggable %pluggable% not found'))
                    ->code('%pluggable%', $pluggable)
            );
        }
    }

    public function getGenerator(): Generator
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
                    ->code('%plug%', get_class($plug))
            );
        }
    }
}
