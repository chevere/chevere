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

namespace Chevere\Parameter;

use ArrayAccess;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ArrayStringParameterInterface;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\IntParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use InvalidArgumentException;
use Throwable;
use function Chevere\Message\message;

function arrayp(
    ParameterInterface ...$required
): ArrayParameterInterface {
    $array = new ArrayParameter();

    return $required
        ? $array->withRequired(...$required)
        : $array;
}

function arrayString(
    StringParameterInterface ...$required
): ArrayStringParameterInterface {
    $array = new ArrayStringParameter();

    return $required
        ? $array->withRequired(...$required)
        : $array;
}

function file(
    ?IntParameterInterface $error = null,
    ?StringParameterInterface $name = null,
    ?StringParameterInterface $type = null,
    ?StringParameterInterface $tmp_name = null,
    ?IntParameterInterface $size = null,
    ?StringParameterInterface $contents = null,
): ArrayParameterInterface {
    $array = arrayp(
        error: $error ?? int(accept: [UPLOAD_ERR_OK]),
        name: $name ?? string(),
        size: $size ?? int(),
        type: $type ?? string(),
        tmp_name: $tmp_name ?? string(),
    );
    if ($contents !== null) {
        $array = $array->withOptional(
            contents: $contents,
        );
    }

    return $array;
}

/**
 * @param ParameterInterface $V Generic value parameter
 * @param ParameterInterface|null $K Generic key parameter
 */
function generic(
    ParameterInterface $V,
    ?ParameterInterface $K = null,
    string $description = '',
): GenericParameterInterface {
    $K ??= int();

    return new GenericParameter($V, $K, $description);
}

/**
 * @param array<int|string, mixed>|ArrayAccess<int|string, mixed> $argument
 * @return array<int|string, mixed> Asserted array, with fixed optional values.
 */
function assertArray(
    ArrayTypeParameterInterface $parameter,
    array|ArrayAccess $argument,
): array {
    return arguments($parameter->parameters(), $argument)->toArray();
}

/**
 * @param array<int|string, string> $argument
 * @return array<int|string, string> Asserted array, with fixed optional values.
 */
function assertArrayString(
    ArrayStringParameterInterface $parameter,
    array $argument,
): array {
    /** @var array<int|string, string> */
    return assertArray($parameter, $argument);
}

function assertNotEmpty(ParameterInterface $expected, mixed $value): void
{
    if ($expected instanceof ArrayTypeParameterInterface
        && empty($value)
        && count($expected->parameters()->requiredKeys()) > 0
    ) {
        throw new InvalidArgumentException(
            (string) message('Argument value provided is empty')
        );
    }
}

// @phpstan-ignore-next-line
function assertGeneric(
    GenericParameterInterface $parameter,
    iterable $argument,
): iterable {
    if (empty($argument)) {
        throw new InvalidArgumentException(
            (string) message('Argument value provided is empty')
        );
    }
    $generic = ' *generic';
    $genericKey = '_K' . $generic;
    $genericValue = '_V' . $generic;
    $expected = $parameter->value();
    // assertNotEmpty($expected, $argument);

    try {
        foreach ($argument as $key => $value) {
            assertNamedArgument($genericKey, $parameter->key(), $key);
            assertNamedArgument($genericValue, $parameter->value(), $value);
        }
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            getThrowableGenericErrorMessage($e->getMessage())
        );
    }

    return $argument;
}

function getThrowableGenericErrorMessage(string $message): string
{
    $strstr = strstr($message, ':', false);
    if (! is_string($strstr)) {
        $strstr = $message; // @codeCoverageIgnore
    } else {
        $strstr = substr($strstr, 2);
    }
    $calledIn = strpos($strstr, ', called in');

    return $calledIn
        ? substr($strstr, 0, $calledIn)
        : $strstr;
}
