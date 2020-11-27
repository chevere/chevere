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

namespace Chevere\Components\Service\Traits;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use ReflectionObject;
use TypeError;
use function Chevere\Components\Type\varType;

trait ServiceDependantTrait
{
    public function getDependencies(): ClassMapInterface
    {
        return new ClassMap;
    }

    public function withDependencies(array $namedArguments): self
    {
        $missing = [];
        $new = clone $this;
        $dependencies = $new->getDependencies();
        foreach ($dependencies->getGenerator() as $className => $key) {
            $value = $namedArguments[$key] ?? null;
            if (!isset($value)) {
                $missing[] = $key;
                continue;
            }
            $this->assertType($className, $key, $value);
            try {
                $new->{$key} = $value;
            } catch (TypeError $e) {
                throw new TypeException(
                    (new Message('Dependency %key% type declaration mismatch'))
                        ->strong('%key%', $key),
                    102
                );
            }
        }
        $new->assertNotMissing($missing);

        return $new;
    }

    final public function assertDependencies(): void
    {
        $dependencies = $this->getDependencies();
        $missing = [];
        /**
         * @var string $type
         * @var string $variable
         */
        foreach ($dependencies->getGenerator() as $type => $variable) {
            if (!isset($this->{$variable})) {
                $missing[] = $variable;
            }
        }
        $this->assertNotMissing($missing);
    }

    private function assertType(string $className, string $key, $value): void
    {
        if (!is_object($value)) {
            throw new TypeException(
                (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
                    ->strong('%key%', $key)
                    ->code('%expected%', $className)
                    ->code('%provided%', varType($key)),
                100
            );
        }
        if (!(new ReflectionObject($value))->isSubclassOf($className)) {
            throw new TypeException(
                (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
                    ->strong('%key%', $key)
                    ->code('%expected%', $className)
                    ->code('%provided%', varType($key)),
                101
            );
        }
    }

    private function assertNotMissing(array $missing): void
    {
        if ($missing !== []) {
            throw new InvalidArgumentException(
                (new Message('Missing dependencies %missing%'))
                    ->code('%missing%', implode(', ', $missing))
            );
        }
    }
}
