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

namespace Chevere\Components\Dependent;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use Chevere\Interfaces\Dependent\DependenciesInterface;
use Ds\Set;
use Generator;

final class Dependencies implements DependenciesInterface
{
    private ClassMapInterface $classMap;

    private Set $keys;

    public function __construct()
    {
        $this->classMap = new ClassMap();
        $this->keys = new Set();
    }

    public function withPut(string ...$namedDependencies): DependenciesInterface
    {
        $new = clone $this;
        $new->keys = new Set();
        foreach ($namedDependencies as $name => $className) {
            $new->classMap = $new->classMap
                ->withPut($className, $name);
        }
        foreach ($new->classMap->getGenerator() as $key) {
            $new->keys->add($key);
        }

        return $new;
    }

    public function keys(): array
    {
        return $this->keys->toArray();
    }

    public function count(): int
    {
        return $this->classMap->count();
    }

    public function getGenerator(): Generator
    {
        foreach ($this->classMap->getGenerator() as $className => $key) {
            yield $key => $className;
        }
    }
}
