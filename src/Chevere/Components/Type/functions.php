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
use Chevere\Interfaces\Message\MessageInterface;
use Chevere\Interfaces\Type\TypeInterface;

/**
 * Same as `gettype` but more "standard" towards `get_debug_type`.
 */
function getType($var): string
{
    $type = \gettype($var);

    return match ($type) {
        'double' => 'float',
        'NULL' => 'null',
        default => $type,
    };
}

function returnTypeExceptionMessage(string $expected, $provided): MessageInterface
{
    return (new Message('Expecting return type %expected%, type %provided% provided'))
        ->code('%expected%', $expected)
        ->code('%provided%', getType($provided));
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

/**
 * @throws InvalidArgumentException
 */
function typeObject(string $className): TypeInterface
{
    $type = new Type($className);
    if (! in_array($type->primitive(), [TypeInterface::PRIMITIVE_CLASS_NAME, TypeInterface::PRIMITIVE_INTERFACE_NAME], true)) {
        throw new InvalidArgumentException(
            (new Message("Class %className% doesn't exists"))
                ->code('%className%', $className)
        );
    }

    return $type;
}
