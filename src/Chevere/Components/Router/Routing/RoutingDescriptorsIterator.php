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

namespace Chevere\Components\Router\Routing;

use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Ds\Set;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;

final class RoutingDescriptorsIterator extends RecursiveFilterIterator
{
    private Set $methodFilenames;

    private Set $knownPaths;

    public function __construct(RecursiveDirectoryIterator $iterator)
    {
        parent::__construct($iterator);

        $this->methodFilenames = new Set();
        $this->knownPaths = new Set();
        foreach (array_keys(RouteEndpointInterface::KNOWN_METHODS) as $method) {
            $this->methodFilenames->add("${method}.php");
        }
    }

    public function accept(): bool
    {
        if ($this->hasChildren()) {
            return true;
        }
        $dirname = dirname((string) $this->current());
        if ($this->knownPaths->contains($dirname)) {
            return false;
        }
        if ($this->methodFilenames->contains($this->current()->getFilename())) {
            $this->knownPaths->add($dirname);

            return true;
        }

        return false;
    }
}
