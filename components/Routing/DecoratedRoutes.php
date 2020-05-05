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
use Countable;
use Ds\Set;
use function DeepCopy\deep_copy;

final class DecoratedRoutes implements Countable
{
    private Set $set;

    private Set $routesPath;

    private Set $routesPathRegex;

    private Set $decoratorFiles;

    public function __construct()
    {
        $this->set = new Set;
        $this->routesPath = new Set;
        $this->routesPathRegex = new Set;
        $this->decoratorFiles = new Set;
    }

    public function withAdd(DecoratedRoute $decoratedRoute): DecoratedRoutes
    {
        if ($this->set->contains($decoratedRoute)) {
            throw new Exception(
                new Message('Instance of object %insterface% has been already added')
            );
        }
        $routePathString = $decoratedRoute->routePath()->toString();
        $routePathRegexString = $decoratedRoute->routePath()->regex()->toString();
        $decoratorFile = $decoratedRoute->routeDecorator()->whereIs();
        if ($this->routesPath->contains($routePathString)) {
            throw new Exception(
                (new Message('Route path %path% has been already added'))
                    ->code('%path%', $routePathString)
            );
        }
        if ($this->routesPathRegex->contains($routePathRegexString)) {
            throw new Exception(
                new Message('Route regex conflict detected, regex %regex% is already registered')
            );
        }

        if ($this->decoratorFiles->contains($routePathString)) {
        }

        $new = clone $this;
        $new->set->add($decoratedRoute);
        $new->routesPath->add($routePathString);
        $new->decoratorFiles->add($decoratorFile);

        return $new;
    }

    public function count(): int
    {
        return $this->set->count();
    }

    public function contains(DecoratedRoute $decoratedRoute): bool
    {
        return $this->set->contains($decoratedRoute);
    }

    public function get(int $position): DecoratedRoute
    {
        return $this->set->get($position);
    }

    public function set(): Set
    {
        return deep_copy($this->set);
    }
}
