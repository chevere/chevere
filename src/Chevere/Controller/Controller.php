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

namespace Chevere\Controller;

use Chevere\Action\Action;
use Chevere\Attribute\Dispatch;
use Chevere\Attribute\Relation;
use Chevere\Controller\Interfaces\Attributes\AttributeInterface;
use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Message\Message;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\StringParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;

/**
 * #[Dispatch('dispatchName')]
 * #[Relation('relationName')]
 */
abstract class Controller extends Action implements ControllerInterface
{
    protected StringParameterInterface $parameter;

    protected string $dispatch = '';

    protected string $relation = '';

    public function __construct()
    {
        $this->parameter = $this->parameter();
        $this->setUp();
        $this->assertParametersType();
        $reflectionClass = new ReflectionClass($this);
        $attributes = $reflectionClass->getAttributes();
        /** @var ReflectionAttribute $attribute */
        foreach ($attributes as $attribute) {
            $this->handleAttribute($attribute);
        }
    }

    private function handleAttribute(ReflectionAttribute $attribute): void
    {
        if ($this->isValidAttribute($attribute, Relation::class)
        ) {
            /** @var AttributeInterface $new */
            $new = $attribute->newInstance();
            $this->relation = $new->attribute();
        }
        if ($this->isValidAttribute($attribute, Dispatch::class)
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
            if ($parameter->type()->validator() !== $this->parameter->type()->validator()) {
                $invalid[] = $name;
            }
        }
        if ($invalid !== []) {
            throw new InvalidArgumentException(
                (new Message('Parameter %parameters% must be of type %type% for controller %className%.'))
                    ->code('%parameters%', implode(', ', $invalid))
                    ->strong('%type%', $this->parameter->type()->typeHinting())
                    ->strong('%className%', static::class)
            );
        }
    }
}
