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

namespace Chevere\Type\Interfaces;

use Chevere\Throwable\Exceptions\InvalidArgumentException;

/**
 * Describes the component in charge of dynamic type validation.
 */
interface TypeInterface
{
    public const BOOLEAN = 'boolean';

    public const INTEGER = 'integer';

    public const FLOAT = 'float';

    public const STRING = 'string';

    public const ARRAY = 'array';

    public const OBJECT = 'object';

    public const CALLABLE = 'callable';

    public const ITERABLE = 'iterable';

    public const RESOURCE = 'resource';

    public const NULL = 'null';

    public const PRIMITIVE_CLASS_NAME = 'className';

    public const PRIMITIVE_INTERFACE_NAME = 'interfaceName';

    /**
     * Type validators [primitive => validator callable]
     * taken from https://www.php.net/manual/en/ref.var.php.
     */
    public const TYPE_VALIDATORS = [
        self::ARRAY => 'is_array',
        self::BOOLEAN => 'is_bool',
        self::CALLABLE => 'is_callable',
        self::FLOAT => 'is_float',
        self::INTEGER => 'is_integer',
        self::ITERABLE => 'is_iterable',
        self::NULL => 'is_null',
        self::OBJECT => 'is_object',
        self::RESOURCE => 'is_resource',
        self::STRING => 'is_string',
        self::PRIMITIVE_CLASS_NAME => 'is_object',
        self::PRIMITIVE_INTERFACE_NAME => 'is_object',
    ];

    /**
     * Type arguments accepted.
     */
    public const TYPE_ARGUMENTS = [
        self::ARRAY,
        self::BOOLEAN,
        self::CALLABLE,
        self::FLOAT,
        self::INTEGER,
        self::ITERABLE,
        self::NULL,
        self::OBJECT,
        self::RESOURCE,
        self::STRING,
    ];

    /**
     * @param string $type A debug type.
     * @throws InvalidArgumentException if the type doesn't exists
     */
    public function __construct(string $type);

    /**
     * Returns the type primitive (array, bool, object, ..., className, interfaceName).
     */
    public function primitive(): string;

    /**
     * Returns the type hinting.
     *
     * It will return either the class name, interface, or the primitive.
     */
    public function typeHinting(): string;

    /**
     * Returns a boolean indicating if `$variable` validates against the type.
     */
    public function validate(mixed $variable): bool;

    /**
     * Returns the validator callable.
     */
    public function validator(): callable;

    /**
     * Returns a boolean indicating if type is scalar.
     */
    public function isScalar(): bool;
}
