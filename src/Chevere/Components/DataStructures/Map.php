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

namespace Chevere\Components\DataStructures;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\DataStructures\MapInterface;
use function DeepCopy\deep_copy;
use Ds\Map as InternalMap;

final class Map implements MapInterface
{
    use MapTrait;

    public function __construct(mixed ...$namedArguments)
    {
        $this->map = new InternalMap();
        /** @var array $namedArguments */
        if (count($namedArguments) > 0) {
            $this->map->putAll($namedArguments);
        }
    }

    public function __clone()
    {
        $this->map = new InternalMap(deep_copy($this->map->toArray()));
    }

    public function withPut(string $key, $value): self
    {
        $new = clone $this;
        $new->map->put($key, $value);

        return $new;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function assertHasKey(string ...$key): void
    {
        $missing = [];
        foreach ($key as $k) {
            if (! $this->map->hasKey($k)) {
                $missing[] = $k;
            }
        }
        if ($missing !== []) {
            throw new OutOfBoundsException(
                (new Message('Missing key(s) %keys%'))
                    ->code('%keys%', implode(', ', $missing))
            );
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $key)
    {
        try {
            return $this->map->get($key);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Key %key% not found'))->code('%key%', $key)
            );
        }
    }
}
