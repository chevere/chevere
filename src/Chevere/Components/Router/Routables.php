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

namespace Chevere\Components\Router;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RoutablesInterface;
use TypeError;
use function Chevere\Components\Type\debugType;
use function Chevere\Components\Type\returnTypeExceptionMessage;

final class Routables implements RoutablesInterface
{
    use MapTrait;

    public function withPut(RoutableInterface $routable): RoutablesInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $key */
        $key = $routable->route()->name()->toString();
        $new->map->put($key, $routable);

        return $new;
    }

    public function has(string $name): bool
    {
        /** @var \Ds\TKey $key */
        $key = $name;

        return $this->map->hasKey($key);
    }

    public function get(string $name): RoutableInterface
    {
        try {
            /** @var RoutableInterface $return */
            $return = $this->map->get($name);

            return $return;
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(
                returnTypeExceptionMessage(RoutableInterface::class, debugType($return))
            );
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Name %name% not found'))
                    ->code('%name%', $name)
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
