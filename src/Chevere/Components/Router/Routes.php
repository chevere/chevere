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
use Chevere\Interfaces\Router\Route\RouteInterface;
use Chevere\Interfaces\Router\RoutesInterface;
use TypeError;

final class Routes implements RoutesInterface
{
    use MapTrait;

    private array $named = [];

    public function withPut(RouteInterface ...$namedRoutes): RoutesInterface
    {
        $new = clone $this;
        foreach ($namedRoutes as $name => $route) {
            $name = strval($name);
            $key = $route->path()->toString();
            $new->map = $new->map->withPut($key, $route);
        }

        return $new;
    }

    public function has(string ...$paths): bool
    {
        return $this->map->has(...$paths);
    }

    /**
     * @throws TypeException
     * @throws OutOfBoundsException
     */
    public function get(string $path): RouteInterface
    {
        try {
            return $this->map->get($path);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Name %name% not found'))
                    ->code('%name%', $path)
            );
        }
    }
}
