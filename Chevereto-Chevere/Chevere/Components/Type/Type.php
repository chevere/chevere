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
use Chevere\Components\Type\Contracts\TypeContract;

/**
 * Type provides type validation toolchain. Usefull to set dynamic types as parameters.
 */
final class Type implements TypeContract
{
    /** @var string The passed argument in construct */
    private string $type;

    /** @var string The dectected primitive type */
    private string $primitive;

    /** @var string The detected class name (if any) */
    // private $className;

    /** @var string The detected interface name (if any) */
    // private $interfaceName;

    /**
     * Creates a new instance.
     *
     * @var string a primitive type, class name or interface
     *
     * @throws TypeNotFoundException if the type doesn't exists
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
        if (in_array($this->primitive, [TypeContract::CLASS_NAME, TypeContract::INTERFACE_NAME])) {
            return $this->type;
        }

        return $this->primitive;
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
        switch ($this->primitive) {
            case TypeContract::CLASS_NAME:
                return $this->isClassName($objectClass);
            case TypeContract::INTERFACE_NAME:
                return $this->isInterfaceImplemented($object);
        }

        return false;
    }

    private function isClassName(string $objectClass): bool
    {
        return $objectClass == $this->type;
    }

    private function isInterfaceImplemented(object $object): bool
    {
        return $object instanceof $this->type;
    }

    private function setPrimitive(): void
    {
        if (isset(TypeContract::TYPE_VALIDATORS[$this->type])) {
            $this->primitive = $this->type;

            return;
        }

        if (class_exists($this->type)) {
            $this->primitive = 'className';
        } elseif (interface_exists($this->type)) {
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
