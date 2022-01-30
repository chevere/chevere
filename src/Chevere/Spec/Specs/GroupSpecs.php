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

namespace Chevere\Spec\Specs;

use Chevere\DataStructure\Traits\MapTrait;
use Chevere\Message\Message;
use Chevere\Spec\Interfaces\Specs\GroupSpecInterface;
use Chevere\Spec\Interfaces\Specs\GroupSpecsInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\OutOfBoundsException;

final class GroupSpecs implements GroupSpecsInterface
{
    use MapTrait;

    public function withPut(GroupSpecInterface $groupSpec): GroupSpecsInterface
    {
        $new = clone $this;
        $new->map = $new->map->withPut($groupSpec->key(), $groupSpec);

        return $new;
    }

    public function has(string $name): bool
    {
        return $this->map->has($name);
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function get(string $name): GroupSpecInterface
    {
        try {
            return $this->map->get($name);
        }
        // @codeCoverageIgnoreStart
        catch (\TypeError $e) {
            throw new TypeError(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Group name %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }
}
