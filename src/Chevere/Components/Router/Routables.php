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

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\RoutablesInterface;
use TypeError;

final class Routables implements RoutablesInterface
{
    use MapTrait;

    public function withPut(RoutableInterface ...$routables): RoutablesInterface
    {
        foreach ($routables as $routable) {
            $new = clone $this;
            $key = $routable->route()->path()->toString();
            $new->map->put($key, $routable);
        }

        return $new;
    }

    public function has(string ...$names): bool
    {
        foreach ($names as $name) {
            if (!$this->map->hasKey($name)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $name): RoutableInterface
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
                (new Message('Name %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }
}
