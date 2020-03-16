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
use Chevere\Components\Spec\Specs\RouteableSpecObjectsRead;
use SplObjectStorage;

final class GroupSpec implements ToArrayInterface
{
    private SplObjectStorage $objects;

    private $array = [];

    /**
     * @var string $specPath /spec/group-name/
     */
    public function __construct(
        string $specPath
    ) {
        $this->jsonPath = $specPath . 'routes.json';
        $this->objects = new SplObjectStorage;
        $this->array = [
            'name' => basename($specPath),
            'spec' => $specPath . 'routes.json',
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

    public function objects(): RouteableSpecObjectsRead
    {
        return new RouteableSpecObjectsRead($this->objects);
    }
}
