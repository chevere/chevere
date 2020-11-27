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
use Chevere\Exceptions\Core\LogicException;
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
        $dependencies = $this->getDependencies();
        $missing = [];
        $new = clone $this;
        foreach ($dependencies->getGenerator() as $className => $key) {
            $value = $namedArguments[$key] ?? null;
            if (!isset($value)) {
                $missing[] = $key;
                continue;
            }
            // if(!is_object($value)) {
            //     throw new TypeException(
            //         (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
            //             ->strong('%key%', $key)
            //             ->code('%expected%', $className)
            //             ->code('%provided%', varType($key))
            //     );
            // }
            $reflection = new ReflectionObject($value);
            if (!$reflection->isSubclassOf($className)) {
                throw new TypeException(
                    (new Message('Expecting dependency %key% of type %expected%, %provided% provided'))
                        ->strong('%key%', $key)
                        ->code('%expected%', $className)
                        ->code('%provided%', varType($key))
                );
            }
            try {
                $new->{$key} = $value;
            } catch (TypeError $e) {
                throw new TypeException(
                    (new Message('Dependency %key% type mismatch'))
                        ->strong('%key%', $key)
                );
            }
        }
        $this->assertNotMissing($missing);

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

    private function assertNotMissing(array $missing): void
    {
        if ($missing !== []) {
            throw new LogicException(
                (new Message('Missing dependencies %missing%'))
                    ->code('%missing%', implode(', ', $missing))
            );
        }
    }
}
