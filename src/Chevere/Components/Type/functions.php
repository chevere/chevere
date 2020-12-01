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
use Chevere\Interfaces\Message\MessageInterface;
use Chevere\Interfaces\Type\TypeInterface;

function varType($var): string
{
    $type = strtolower(gettype($var));
    if ($type === 'double') {
        return 'float';
    }

    return $type;
}

function debugType($var): string
{
    $type = varType($var);
    if ($type === 'object') {
        return get_class($var);
    }

    return $type;
}

function returnTypeExceptionMessage(string $expected, string $provided): MessageInterface
{
    return (new Message('Expecting return type %expected%, type %provided% provided'))
        ->code('%expected%', $expected)
        ->code('%provided%', $provided);
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

function typeObject(): TypeInterface
{
    return new Type(Type::OBJECT);
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
