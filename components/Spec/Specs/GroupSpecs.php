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

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Spec\Specs\GroupSpecInterface;
use Chevere\Interfaces\Spec\Specs\GroupSpecsInterface;
use TypeError;
use function Chevere\Components\Type\debugType;
use function Chevere\Components\Type\returnTypeExceptionMessage;

final class GroupSpecs implements GroupSpecsInterface
{
    use MapTrait;

    public function withPut(GroupSpecInterface $groupSpec): GroupSpecsInterface
    {
        $new = clone $this;
        $new->map->put($groupSpec->key(), $groupSpec);

        return $new;
    }

    public function has(string $name): bool
    {
        return $this->map->hasKey($name);
    }

    public function get(string $name): GroupSpecInterface
    {
        try {
            /** @var GroupSpecInterface $return */
            $return = $this->map->get($name);

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage(GroupSpecInterface::class, debugType($return))
            );
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
