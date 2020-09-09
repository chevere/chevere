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
use Chevere\Interfaces\Spec\Specs\RoutableSpecInterface;
use Chevere\Interfaces\Spec\Specs\RoutableSpecsInterface;
use TypeError;
use function Chevere\Components\Type\debugType;
use function Chevere\Components\Type\returnTypeExceptionMessage;

final class RoutableSpecs implements RoutableSpecsInterface
{
    use MapTrait;

    public function withPut(RoutableSpecInterface $routableSpec): RoutableSpecsInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $key */
        $key = $routableSpec->key();
        $new->map->put($key, $routableSpec);

        return $new;
    }

    public function has(string $routeName): bool
    {
        return $this->map->hasKey($routeName);
    }

    public function get(string $routeName): RoutableSpecInterface
    {
        try {
            /** @var RoutableSpecInterface $return */
            $return = $this->map->get($routeName);

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage(RoutableSpecInterface::class, debugType($return))
            );
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Route name %routeName% not found'))
                    ->code('%routeName%', $routeName)
            );
        }
    }
}
