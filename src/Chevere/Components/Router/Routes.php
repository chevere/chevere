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

use Chevere\Components\DataStructure\Map;
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

    private Map $named;

    public function __construct()
    {
        $this->map = new Map();
        $this->named = new Map();
    }

    public function __clone()
    {
        $this->map = clone $this->map;
        $this->named = clone $this->named;
    }

    public function withPut(RouteInterface ...$namedRoutes): RoutesInterface
    {
        $new = clone $this;
        foreach ($namedRoutes as $name => $route) {
            $name = strval($name);
            $key = $route->path()->toString();
            $new->map = $new->map->withPut($key, $route);
            $new->named = $new->named->withPut($key, $name);
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
                (new Message('Path %path% not found'))
                    ->code('%path%', $path)
            );
        }
    }

    public function getName(string $path): string
    {
        try {
            return $this->named->get($path);
        }
        // @codeCoverageIgnoreStart
        catch (TypeError $e) {
            throw new TypeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
        catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Path %path% not found'))
                    ->code('%path%', $path)
            );
        }
    }
}
