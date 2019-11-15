<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Type;

use Chevere\Components\Message\Message;
use Chevere\Components\Type\Exceptions\TypeNotFoundException;
use Chevere\Contracts\Type\TypeContract;

/**
 * Type provides type validation toolchain. Usefull to set dynamic types as parameters.
 */
final class Type implements TypeContract
{
    /** @var string The passed argument in construct */
    private $type;

    /** @var string The dectected primitive type */
    private $primitive;

    /** @var string The detected class name (if any) */
    private $className;

    /** @var string The detected interface name (if any) */
    private $interfaceName;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $type)
    {
        $this->type = $type;
        $this->setPrimitive();
        $this->assertPrimitive();
    }

    /**
     * {@inheritdoc}
     */
    public function primitive(): string
    {
        return $this->primitive;
    }

    /**
     * {@inheritdoc}
     */
    public function typeHinting(): string
    {
        return $this->className ?? $this->interfaceName ?? $this->primitive;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($var): bool
    {
        if ($this->isAbleToValidateObjects()) {
            return $this->validateObject($var);
        }

        return $this->validator()($var);
    }

    /**
     * {@inheritdoc}
     */
    public function validator(): callable
    {
        return TypeContract::TYPE_VALIDATORS[$this->primitive];
    }

    private function isAbleToValidateObjects(): bool
    {
        return in_array($this->primitive, [TypeContract::CLASS_NAME, TypeContract::INTERFACE_NAME]);
    }

    private function validateObject(object $object): bool
    {
        $objectClass = get_class($object);
        switch (true) {
            case isset($this->className, $this->interfaceName):
                return $this->isClassName($objectClass) || $this->isInterfaceInstance($object);
            case isset($this->className):
                return $this->isClassName($objectClass);
            case isset($this->interfaceName):
                return $this->isInterfaceInstance($object);
        }

        return false;
    }

    private function isClassName(string $objectClass): bool
    {
        return $objectClass == $this->className;
    }

    private function isInterfaceInstance(object $object): bool
    {
        return $object instanceof $this->interfaceName;
    }

    private function setPrimitive(): void
    {
        if (isset(TypeContract::TYPE_VALIDATORS[$this->type])) {
            $this->primitive = $this->type;

            return;
        }
        $this->handleClassName();
        $this->handleInterfaceName();
        $this->handlePrimitiveClassName();
        $this->handlePrimitiveInterfaceName();
    }

    private function handleClassName(): void
    {
        if (class_exists($this->type)) {
            $this->className = $this->type;
        }
    }

    private function handleInterfaceName(): void
    {
        if (interface_exists($this->type)) {
            $this->interfaceName = $this->type;
        }
    }

    private function handlePrimitiveClassName(): void
    {
        if (isset($this->className)) {
            $this->primitive = 'className';
        }
    }

    private function handlePrimitiveInterfaceName(): void
    {
        if (isset($this->interfaceName)) {
            $this->primitive = 'interfaceName';
        }
    }

    private function assertPrimitive(): void
    {
        if (null === $this->primitive) {
            throw new TypeNotFoundException(
                (new Message('Type %type% not found'))
                    ->code('%type%', $this->type)
                    ->toString()
            );
        }
    }
}
