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

namespace Chevere\Components\Type;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Type\TypeInterface;

final class Type implements TypeInterface
{
    private string $validator;

    private string $primitive = '';

    private string $typeHinting;

    public function __construct(
        private string $type
    ) {
        $this->setPrimitive();
        $this->assertHasPrimitive();
        $this->validator = self::TYPE_VALIDATORS[$this->primitive];
        $this->typeHinting = $this->primitive;
        if (in_array($this->primitive, [self::PRIMITIVE_CLASS_NAME, self::PRIMITIVE_INTERFACE_NAME], true)) {
            $this->typeHinting = $this->type;
        }
    }

    public function validator(): callable
    {
        /** @var callable */
        return $this->validator;
    }

    public function primitive(): string
    {
        return $this->primitive;
    }

    public function typeHinting(): string
    {
        return $this->typeHinting;
    }

    public function validate($var): bool
    {
        if (is_object($var) && $this->isAbleToValidateObjects()) {
            return $this->validateObject($var);
        }

        return $this->validator()($var);
    }

    public function isScalar(): bool
    {
        return in_array($this->primitive, ['boolean', 'integer', 'float', 'string'], true);
    }

    private function isAbleToValidateObjects(): bool
    {
        return in_array(
            $this->primitive,
            [self::PRIMITIVE_CLASS_NAME, self::PRIMITIVE_INTERFACE_NAME],
            true
        );
    }

    private function validateObject(object $object): bool
    {
        $objectClass = get_class($object);

        return match ($this->primitive) {
            self::PRIMITIVE_CLASS_NAME => $this->isClassName($objectClass),
            default => $this->isInterfaceImplemented($object),
        };
    }

    private function isClassName(string $objectClass): bool
    {
        return $this->type === $objectClass;
    }

    private function isInterfaceImplemented(object $object): bool
    {
        return $object instanceof $this->type;
    }

    private function setPrimitive(): void
    {
        if (isset(self::TYPE_VALIDATORS[$this->type]) && in_array($this->type, self::TYPE_ARGUMENTS)) {
            $this->primitive = $this->type;

            return;
        }

        if (class_exists($this->type)) {
            $this->primitive = self::PRIMITIVE_CLASS_NAME;
        } elseif (interface_exists($this->type)) {
            $this->primitive = self::PRIMITIVE_INTERFACE_NAME;
        }
    }

    private function assertHasPrimitive(): void
    {
        if ($this->primitive === '') {
            throw new InvalidArgumentException(
                (new Message("Type %type% doesn't exists"))
                    ->code('%type%', $this->type)
            );
        }
    }
}
