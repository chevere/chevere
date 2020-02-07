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

namespace Chevere\Components\Type\Interfaces;

interface TypeInterface
{
    /** Scalar */
    const BOOLEAN = 'boolean';
    const INTEGER = 'integer';
    const FLOAT = 'float';
    const STRING = 'string';

    /** Compound */
    const ARRAY = 'array';
    const OBJECT = 'object';
    const CALLABLE = 'callable';
    const ITERABLE = 'iterable';

    /** Special */
    const RESOURCE = 'resource';
    const NULL = 'null';

    /** Pseudo-types */
    const CLASS_NAME = 'className';
    const INTERFACE_NAME = 'interfaceName';

    /**
     * Type validators [primitive => validator callable]
     * taken from https://www.php.net/manual/en/ref.var.php.
     */
    const TYPE_VALIDATORS = [
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
        self::CLASS_NAME => 'is_object',
        self::INTERFACE_NAME => 'is_object',
    ];

    public function __construct(string $type);

    /**
     * Returns the type primitive (array, bool, object, ..., clasName, interfaceName).
     */
    public function primitive(): string;

    /**
     * Returns the type hinting.
     *
     * It will return either the class name, interface, or simply the primitive.
     */
    public function typeHinting(): string;

    /**
     * Returns a boolean indicating if $var validates agains the type.
     */
    public function validate($var): bool;

    /**
     * Returns the type validator callable.
     */
    public function validator(): callable;
}
