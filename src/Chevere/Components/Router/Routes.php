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
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Chevere\Interfaces\Router\RoutesInterface;
use TypeError;

final class Routes implements RoutesInterface
{
    use MapTrait;

    private Map $names;

    public function withAdded(RouteInterface ...$routes): RoutesInterface
    {
        $new = clone $this;
        $new->names ??= new Map();
        foreach ($routes as $route) {
            $key = $route->path()->toString();
            $new->assertRoute($key, $route);
            $new->names = $new->names
                ->withPut($route->name(), $key);
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
                (new Message('Path %path% not found'))
                    ->code('%path%', $path)
            );
        }
    }

    private function assertRoute(string $path, RouteInterface $route): void
    {
        if ($this->names->has($route->name())) {
            throw new OverflowException(
                code: static::EXCEPTION_CODE_TAKEN_NAME,
                message: (new Message('Named route %name% has been already taken.'))
                    ->code('%name%', $route->name())
            );
        }
        if ($this->map->has($path)) {
            throw new OverflowException(
                code: static::EXCEPTION_CODE_TAKEN_PATH,
                message: (new Message('Route path %path% has been already taken.'))
                    ->code('%path%', $route->name())
            );
        }
    }
}
