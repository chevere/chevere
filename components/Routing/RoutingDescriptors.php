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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\RangeException;
use Chevere\Exceptions\Routing\DecoratedRouteAlreadyAddedException;
use Chevere\Exceptions\Routing\RouteNameAlreadyAddedException;
use Chevere\Exceptions\Routing\RoutePathAlreadyAddedException;
use Chevere\Exceptions\Routing\RouteRegexAlreadyAddedException;
use Chevere\Interfaces\Routing\RoutingDescriptorInterface;
use Chevere\Interfaces\Routing\RoutingDescriptorsInterface;
use Ds\Set;
use OutOfRangeException;

final class RoutingDescriptors implements RoutingDescriptorsInterface
{
    private Set $set;

    private array $routesPath = [];

    private array $routesName = [];

    private array $routesPathRegex = [];

    private RoutingDescriptorInterface $descriptor;

    private int $pos = -1;

    public function __construct()
    {
        $this->set = new Set;
    }

    public function withAdded(RoutingDescriptorInterface $descriptor): RoutingDescriptorsInterface
    {
        if ($this->set->contains($descriptor)) {
            throw new DecoratedRouteAlreadyAddedException(
                (new Message('Instance of object %object% has been already added'))
                    ->code('%object%', get_class($descriptor) . '#' . spl_object_id($descriptor))
            );
        }
        $new = clone $this;
        $new->descriptor = $descriptor;
        $new->pos++;
        try {
            $new->assertPushPath($descriptor->path()->toString());
            $new->assertPushName($descriptor->decorator()->name()->toString());
            $new->assertPushRegex($descriptor->path()->regex()->toString());
        } catch (Exception $e) {
            throw new $e(
                $e->message()->code(
                    '%by%',
                    $this->get($e->getCode())->dir()->path()->absolute()
                )
            );
        }

        $new->set->add($descriptor);

        return $new;
    }

    public function count(): int
    {
        return $this->set->count();
    }

    public function contains(RoutingDescriptorInterface $descriptor): bool
    {
        return $this->set->contains($descriptor);
    }

    /**
     * @throws OutOfRangeException
     * @throws RangeException
     */
    public function get(int $position): RoutingDescriptorInterface
    {
        $return = $this->set->get($position);
        if ($return === null) {
            throw new RangeException; // @codeCoverageIgnore
        }

        return $return;
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
}
