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

namespace Chevere\Components\DataStructure;

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use function Chevere\Components\Var\deepCopy;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\DataStructure\MapInterface;
use Ds\Map as DsMap;

final class Map implements MapInterface
{
    use MapTrait;

    public function __construct(mixed ...$namedArguments)
    {
        $this->map = new DsMap();
        /** @var array $namedArguments */
        if (count($namedArguments) > 0) {
            $this->map->putAll($namedArguments);
        }
    }

    public function __clone()
    {
        $this->map = new DsMap(deepCopy($this->map->toArray()));
    }

    public function withPut(mixed ...$namedValues): self
    {
        $new = clone $this;
        foreach ($namedValues as $name => $value) {
            $new->map->put($name, $value);
        }

        return $new;
    }

    public function has(string ...$keys): bool
    {
        try {
            $this->assertHas(...$keys);

            return true;
        } catch (OutOfBoundsException) {
            return false;
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    public function assertHas(string ...$keys): void
    {
        $missing = [];
        foreach ($keys as $k) {
            if (!$this->map->hasKey($k)) {
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
