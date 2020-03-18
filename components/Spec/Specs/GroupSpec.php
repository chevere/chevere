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

namespace Chevere\Components\Spec;

use Chevere\Components\Common\Interfaces\ToArrayInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\Specs\RouteableSpecObjectsRead;
use SplObjectStorage;

final class GroupSpec implements ToArrayInterface
{
    private string $jsonPath;

    private SplObjectStorage $objects;

    private $array = [];

    /**
     * @var string SpecPathInterface /spec/group-name
     */
    public function __construct(SpecPathInterface $specPath)
    {
        $this->jsonPath = $specPath->getChild('routes.json')->pub();
        $this->objects = new SplObjectStorage;
        $this->array = [
            'name' => basename($specPath->pub()),
            'spec' => $this->jsonPath,
            'routes' => [],
        ];
    }

    public function withAddedRouteable(RouteableSpec $routeableSpec): GroupSpec
    {
        $new = clone $this;
        $this->objects->attach($routeableSpec);
        $new->array['routes'][] = $routeableSpec->toArray();

        return $new;
    }

    public function jsonPath(): string
    {
        return $this->jsonPath;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function routeableSpecs(): RouteableSpecObjectsRead
    {
        return new RouteableSpecObjectsRead($this->objects);
    }
}
