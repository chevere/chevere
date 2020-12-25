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

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Message\Message;
use function Chevere\Components\Type\debugType;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Exceptions\Dependent\DependentException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use ReflectionObject;
use TypeError;

trait DependentTrait
{
    private ClassMapInterface $dependencies;

    public function __construct(mixed ...$namedDependency)
    {
        $this->setDependency(...$namedDependency);
    }

    public function getDependencies(): ClassMapInterface
    {
        return new ClassMap();
    }

    final public function assertDependencies(): void
    {
        $dependencies = $this->dependencies ?? $this->getDependencies();
        $missing = [];
        foreach ($dependencies->keys() as $property) {
            if (! isset($this->{$property})) {
                $missing[] = $property;
            }
        }
        $this->assertNotMissing($missing);
    }

    private function setDependency(mixed ...$namedArguments): void
    {
        $missing = [];
        $this->dependencies = $this->getDependencies();
        foreach ($this->dependencies->getGenerator() as $className => $key) {
            $value = $namedArguments[$key] ?? null;
            if (! isset($value)) {
                $missing[] = $key;

                continue;
            }
            $this->assertType($className, $key, $value);

            try {
                $this->{$key} = $value;
            } catch (TypeError $e) {
                throw new TypeException(
                    (new Message('Dependency %key% type declaration mismatch'))
                        ->strong('%key%', $key),
                    102
                );
            }
        }

        $this->assertNotMissing($missing);
    }

    private function assertType(string $className, string $key, $value): void
    {
        if (! is_object($value)) {
            throw new TypeException(
                (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
                    ->strong('%key%', $key)
                    ->code('%expected%', $className)
                    ->code('%provided%', debugType($key)),
                100
            );
        }
        if (! (new ReflectionObject($value))->isSubclassOf($className)) {
            throw new TypeException(
                (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
                    ->strong('%key%', $key)
                    ->code('%expected%', $className)
                    ->code('%provided%', debugType($value)),
                101
            );
        }
    }

    private function assertNotMissing(array $missing): void
    {
        if ($missing !== []) {
            throw new DependentException(
                (new Message('Missing dependencies %missing%'))
                    ->code('%missing%', implode(', ', $missing))
            );
        }
    }
}
