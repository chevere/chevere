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

/**
 * Get variable type.
 */
function getType(mixed $variable): string
{
    $type = \gettype($variable);

    return match ($type) {
        'integer' => 'int',
        'boolean' => 'bool',
        'double' => 'float',
        'NULL' => 'null',
        default => $type,
    };
}

function returnTypeExceptionMessage(string $expected, mixed $provided): string
{
    return strtr(
        'Expecting return type `%expected%`, type `%provided%` provided',
        [
            '%expected%' => $expected,
            '%provided%' => getType($provided),
        ]
    );
}

function typeBool(): TypeInterface
{
    return new Type(Type::BOOL);
}

function typeInt(): TypeInterface
{
    return new Type(Type::INT);
}

function typeFloat(): TypeInterface
{
    return new Type(Type::FLOAT);
}

function typeString(): TypeInterface
{
    return new Type(Type::STRING);
}

function typeArray(): TypeInterface
{
    return new Type(Type::ARRAY);
}

function typeCallable(): TypeInterface
{
    return new Type(Type::CALLABLE);
}

function typeIterable(): TypeInterface
{
    return new Type(Type::ITERABLE);
}

function typeResource(): TypeInterface
{
    return new Type(Type::RESOURCE);
}

function typeNull(): TypeInterface
{
    return new Type(Type::NULL);
}

function typeUnion(): TypeInterface
{
    return new Type(Type::UNION);
}

function typeGeneric(): TypeInterface
{
    return new Type(Type::GENERIC);
}

/**
 * @throws InvalidArgumentException
 */
function typeObject(string $className): TypeInterface
{
    return new Type($className);
}
