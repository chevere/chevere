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

namespace Chevere\Components\Controller;

use Chevere\Components\Action\Action;
use Chevere\Components\Attribute\Dispatch;
use Chevere\Components\Attribute\Relation;
use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Controller\Attributes\AttributeInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;
use ReflectionAttribute;
use ReflectionClass;

/**
 * #[Dispatch('dispatchName')]
 * #[Relation('relationName')]
 */
abstract class Controller extends Action implements ControllerInterface
{
    protected StringParameterInterface $parameterType;

    public function __construct(
        protected string $dispatch = '',
        protected string $relation = '',
    ) {
        $this->parameterType ??= $this->parameter();
        $this->setUp();
        $this->assertParametersType();
        if (in_array('', [$this->relation, $this->dispatch])) {
            $reflectionClass = new ReflectionClass($this);
            $attributes = $reflectionClass->getAttributes();
            /** @var ReflectionAttribute $attribute */
            foreach ($attributes as $attribute) {
                $this->handleAttribute($attribute);
            }
        }
    }

    private function handleAttribute(ReflectionAttribute $attribute): void
    {
        if (
            $this->relation === ''
            && $this->isValidAttribute($attribute, Relation::class)
        ) {
            /** @var AttributeInterface $new */
            $new = $attribute->newInstance();
            $this->relation = $new->attribute();
        }
        if (
            $this->dispatch === ''
            && $this->isValidAttribute($attribute, Dispatch::class)
        ) {
            /** @var AttributeInterface $new */
            $new = $attribute->newInstance();
            $this->dispatch = $new->attribute();
        }
    }

    private function isValidAttribute(ReflectionAttribute $attribute, string $className): bool
    {
        return $attribute->getName() === $className
            || is_subclass_of($attribute->getName(), $className);
    }

    public function parameter(): StringParameterInterface
    {
        return new StringParameter();
    }

    final public function relation(): string
    {
        return $this->relation;
    }

    final public function dispatch(): string
    {
        return $this->dispatch;
    }

    private function assertParametersType(): void
    {
        $invalid = [];
        foreach ($this->parameters()->getIterator() as $name => $parameter) {
            if ($parameter->type()->validator() !== $this->parameterType->type()->validator()) {
                $invalid[] = $name;
            }
        }
        if ($invalid !== []) {
            throw new InvalidArgumentException(
                (new Message('Parameter %parameters% must be of type %type% for controller %className%.'))
                    ->code('%parameters%', implode(', ', $invalid))
                    ->strong('%type%', $this->parameterType->type()->typeHinting())
                    ->strong('%className%', static::class)
            );
        }
    }
}
