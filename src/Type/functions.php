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

use Chevere\Message\Interfaces\MessageInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Message\message;

/**
 * Same as `gettype` but more "standard" towards `get_debug_type`.
 */
function getType(mixed $variable): string
{
    $type = \gettype($variable);

    return match ($type) {
        'double' => 'float',
        'NULL' => 'null',
        default => $type,
    };
}

function toStandardType(string $type): string
{
    return TypeInterface::TYPE_STANDARD[$type]
        ?? $type;
}

function returnTypeExceptionMessage(string $expected, mixed $provided): MessageInterface
{
    return message('Expecting return type %expected%, type %provided% provided')
        ->withCode('%expected%', $expected)
        ->withCode('%provided%', getType($provided));
}

function typeBoolean(): TypeInterface
{
    return new Type(Type::BOOLEAN);
}

function typeInteger(): TypeInterface
{
    return new Type(Type::INTEGER);
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

function typeFile(): TypeInterface
{
    return new Type(Type::FILE);
}

/**
 * @throws InvalidArgumentException
 */
function typeObject(string $className): TypeInterface
{
    return new Type($className);
}
