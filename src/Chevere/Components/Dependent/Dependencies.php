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
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
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
        $new->addKeys();

        return $new;
    }

    public function withMerge(DependenciesInterface $dependencies): DependenciesInterface
    {
        $new = clone $this;
        foreach ($dependencies->getGenerator() as $name => $className) {
            if ($new->keys->contains($name)) {
                $expected = $new->key($name);
                if ($expected !== $className) {
                    throw new OverflowException(
                        message: (new Message('Attempting to re-declare named dependency %named% type from %expected% to %provided%'))
                            ->strong('%named%', $name)
                            ->code('%expected%', $expected)
                            ->code('%provided%', $className)
                    );
                }
            } else {
                $new->classMap = $new->classMap
                    ->withPut($className, $name);
            }
        }
        $new->keys = new Set();
        $new->addKeys();

        return $new;
    }

    public function hasKey(string $key): bool
    {
        return $this->keys->contains($key);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function key(string $key): string
    {
        return $this->classMap->className($key);
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

    private function addKeys(): void
    {
        foreach ($this->classMap->getGenerator() as $key) {
            $this->keys->add($key);
        }
    }
}
