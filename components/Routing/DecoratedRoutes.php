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

namespace Chevere\Components\Routing;

use Chevere\Components\ExceptionHandler\Exceptions\Exception;
use Chevere\Components\Message\Message;
use Chevere\Components\Routing\Exceptions\DecoratedRouteAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RouteDecoratorFileAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RouteNameAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RoutePathAlreadyAddedException;
use Chevere\Components\Routing\Exceptions\RouteRegexAlreadyAddedException;
use Chevere\Components\Routing\Interfaces\DecoratedRouteInterface;
use Chevere\Components\Routing\Interfaces\DecoratedRoutesInterface;
use Ds\Set;
use OutOfRangeException;

final class DecoratedRoutes implements DecoratedRoutesInterface
{
    private Set $set;

    private array $routesPath = [];

    private array $routesName = [];

    private array $routesPathRegex = [];

    private array $decoratorFiles = [];

    private int $pos = -1;

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withDecorated(DecoratedRouteInterface $decoratedRoute): DecoratedRoutesInterface
    {
        if ($this->set->contains($decoratedRoute)) {
            throw new DecoratedRouteAlreadyAddedException(
                (new Message('Instance of object %object% has been already added'))
                    ->code('%object%', get_class($decoratedRoute) . '#' . spl_object_id($decoratedRoute))
            );
        }
        $new = clone $this;
        $new->decoratedRoute = $decoratedRoute;
        $new->pos++;
        $new->assertPushDecoratorFile($decoratedRoute->routeDecorator()->whereIs());
        try {
            $new->assertPushPath($decoratedRoute->routePath()->toString());
            $new->assertPushName($decoratedRoute->routeDecorator()->name()->toString());
            $new->assertPushRegex($decoratedRoute->routePath()->regex()->toString());
        } catch (Exception $e) {
            throw new $e(
                $e->message()->code('%by%', $this->get($e->getCode())->routeDecorator()->whereIs())
            );
        }

        $new->set->add($decoratedRoute);

        return $new;
    }

    public function count(): int
    {
        return $this->set->count();
    }

    public function contains(DecoratedRouteInterface $decoratedRoute): bool
    {
        return $this->set->contains($decoratedRoute);
    }

    /**
     * @throws OutOfRangeException
     */
    public function get(int $position): DecoratedRouteInterface
    {
        return $this->set->get($position);
    }

    private function assertPushPath(string $path): void
    {
        $pos = $this->routesPath[$path] ?? null;
        if (isset($pos)) {
            throw new RoutePathAlreadyAddedException(
                (new Message('Route path %path% has been already added by %by%'))
                    ->code('%path%', $path),
                $pos
            );
        }
        $this->routesPath[$path] = $this->pos;
    }

    private function assertPushName(string $name): void
    {
        $pos = $this->routesName[$name] ?? null;
        if (isset($pos)) {
            throw new RouteNameAlreadyAddedException(
                (new Message('Route %name% has been already added by %by%'))
                    ->code('%name%', $name),
                $pos
            );
        }
        $this->routesName[$name] = $this->pos;
    }

    private function assertPushRegex(string $regex): void
    {
        $pos = $this->routesPathRegex[$regex] ?? null;
        if (isset($pos)) {
            throw new RouteRegexAlreadyAddedException(
                (new Message('Route regex %regex% has been already added by %by%'))
                    ->code('%regex%', $regex),
                $pos
            );
        }
        $this->routesPathRegex[$regex] = $this->pos;
    }

    private function assertPushDecoratorFile(string $file): void
    {
        $pos = $this->decoratorFiles[$file] ?? null;
        if (isset($pos)) {
            throw new RouteDecoratorFileAlreadyAddedException(
                (new Message('Route decorator file %path% has been already added'))
                    ->code('%path%', $file),
                $pos
            );
        }
        $this->decoratorFiles[$file] = $this->pos;
    }
}
