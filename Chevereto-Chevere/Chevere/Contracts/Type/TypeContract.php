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

namespace Chevere\Contracts\Type;

interface TypeContract
{
    /**
     * Type validators [primitive => validator callable]
     * taken from https://www.php.net/manual/en/ref.var.php.
     */
    const TYPE_VALIDATORS = [
        'array' => 'is_array',
        'bool' => 'is_bool',
        'callable' => 'is_callable',
        'countable' => 'is_countable',
        'double' => 'is_double',
        'float' => 'is_float',
        'int' => 'is_int',
        'integer' => 'is_integer',
        'iterable' => 'is_iterable',
        'long' => 'is_long',
        'null' => 'is_null',
        'numeric' => 'is_numeric',
        'object' => 'is_object',
        'real' => 'is_real',
        'resource' => 'is_resource',
        'scalar' => 'is_scalar',
        'string' => 'is_string',
    ];

    /**
     * Creates a new instance.
     *
     * @var string a primitive type, class name or interface
     */
    public function __construct(string $type);

    /**
     * Returns the type primitive (array, bool, object, etc.).
     */
    public function primitive(): string;

    /**
     * Returns the type hinting.
     *
     * It will return either the class name, interface, or simply the primitive.
     */
    public function typeHinting(): string;

    /**
     * Validate an object against the instance class and interface (if any).
     *
     * @param object $object The object to validate
     */
    public function validateObject(object $object): bool;

    /**
     * Validate a variable against the instance primitive.
     *
     * @param mixed $var The variable to validate
     */
    public function validatePrimitive($var): bool;

    /**
     * Returns the type validator callable.
     */
    public function validator(): callable;
}
