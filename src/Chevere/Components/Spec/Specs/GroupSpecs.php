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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Spec\Specs\GroupSpecInterface;
use Chevere\Interfaces\Spec\Specs\GroupSpecsInterface;
use TypeError;

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
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $name): GroupSpecInterface
    {
        try {
            return $this->map->get($name);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
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
