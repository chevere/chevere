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

namespace Chevere\Type;

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Message\message;

final class Type implements TypeInterface
{
    public const CLASS_TYPES = [self::PRIMITIVE_CLASS_NAME, self::PRIMITIVE_INTERFACE_NAME];

    private string $validator;

    private string $primitive = '';

    private string $typeHinting;

    /**
     * @param string $type A debug type
     */
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

    public function validate(mixed $variable): bool
    {
        if (is_object($variable) && $this->isAbleToValidateObjects()) {
            return $this->validateObject($variable);
        }

        return $this->validator()($variable);
    }

    public function isScalar(): bool
    {
        return in_array($this->primitive, ['boolean', 'integer', 'float', 'string'], true);
    }

    private function isAbleToValidateObjects(): bool
    {
        return in_array($this->primitive, self::CLASS_TYPES, true);
    }

    private function validateObject(object $object): bool
    {
        return $object instanceof $this->type;
    }

    private function setPrimitive(): void
    {
        if (in_array($this->type, self::TYPE_ARGUMENTS, true)) {
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
                message("Type %type% doesn't exists")
                    ->withCode('%type%', $this->type)
            );
        }
    }
}
