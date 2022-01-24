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

namespace Chevere\Dependent\Traits;

use Chevere\Dependent\Dependencies;
use Chevere\Dependent\Exceptions\MissingDependenciesException;
use Chevere\Dependent\Interfaces\DependenciesInterface;
use Chevere\Message\Message;
use Chevere\Throwable\Errors\TypeError;

trait DependentTrait
{
    private DependenciesInterface $dependencies;

    public function withDependencies(object ...$namedDependencies): static
    {
        $new = clone $this;
        $missing = [];
        $new->dependencies ??= $new->getDependencies();
        foreach ($new->dependencies->getIterator() as $name => $className) {
            $value = $namedDependencies[$name] ?? null;
            if (!isset($value)) {
                $missing[] = $name;

                continue;
            }
            /** @var object $value */
            $new->assertType($className, $name, $value);

            try {
                $new->{$name} = $value;
            } catch (\TypeError) {
                throw new TypeError(
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
            if (!isset($this->{$property})) {
                $missing[] = $property;
            }
        }
        $this->assertNotMissing($missing);
    }

    final public function dependencies(): DependenciesInterface
    {
        return $this->dependencies ??= $this->getDependencies();
    }

    private function assertType(string $className, string $name, object $value): void
    {
        if (!is_a($value, $className, false)) {
            throw new TypeError(
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
