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

namespace Chevere\Components\Dependent\Traits;

use Chevere\Components\Dependent\Dependencies;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Exceptions\Dependent\MissingDependenciesException;
use Chevere\Interfaces\Dependent\DependenciesInterface;
use ReflectionObject;
use TypeError;

trait DependentTrait
{
    private DependenciesInterface $dependencies;

    public function withDependencies(object ...$namedDependencies): self
    {
        $new = clone $this;
        $missing = [];
        $new->dependencies ??= $new->getDependencies();
        foreach ($new->dependencies->getGenerator() as $name => $className) {
            $value = $namedDependencies[$name] ?? null;
            if (! isset($value)) {
                $missing[] = $name;

                continue;
            }
            /** @var object $value */
            $new->assertType($className, $name, $value);

            try {
                $new->{$name} = $value;
            } catch (TypeError $e) {
                throw new TypeException(
                    (new Message('Dependency %key% type declaration mismatch'))
                        ->strong('%key%', $name)
                );
            }
        }
        $new->assertNotMissing($missing);

        return $new;
    }

    public function getDependencies(): DependenciesInterface
    {
        return new Dependencies();
    }

    final public function assertDependencies(): void
    {
        $this->dependencies ??= $this->getDependencies();
        $missing = [];
        foreach ($this->dependencies->keys() as $property) {
            if (! isset($this->{$property})) {
                $missing[] = $property;
            }
        }
        $this->assertNotMissing($missing);
    }

    final public function dependencies(): DependenciesInterface
    {
        return $this->dependencies;
    }

    private function assertType(string $className, string $name, object $value): void
    {
        if (! (new ReflectionObject($value))->isSubclassOf($className)) {
            throw new TypeException(
                (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
                    ->strong('%key%', $name)
                    ->code('%expected%', $className)
                    ->code('%provided%', get_debug_type($value)),
            );
        }
    }

    private function assertNotMissing(array $missing): void
    {
        if ($missing !== []) {
            throw new MissingDependenciesException(
                (new Message('Missing dependencies %missing%'))
                    ->code('%missing%', implode(', ', $missing))
            );
        }
    }
}
