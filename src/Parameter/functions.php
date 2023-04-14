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

use Chevere\Message\Interfaces\MessageInterface;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Regex\Regex;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Throwable;

/**
 * @param ParameterInterface ...$parameter Required parameters
 */
function arrayp(
    ParameterInterface ...$parameter
): ArrayParameterInterface {
    $array = new ArrayParameter();

    return $parameter
        ? $array->withRequired(...$parameter)
        : $array;
}

/**
 * @param ParameterInterface ...$parameter Optional parameters
 */
function arrayop(
    ParameterInterface ...$parameter
): ArrayParameterInterface {
    $array = new ArrayParameter();

    return $parameter
        ? $array->withOptional(...$parameter)
        : $array;
}

function booleanp(
    string $description = '',
    bool $default = false,
): BooleanParameterInterface {
    $parameter = new BooleanParameter($description);

    return $parameter->withDefault($default);
}

function nullp(
    string $description = '',
): NullParameterInterface {
    return new NullParameter($description);
}

/**
 * @param float[] $accept
 */
function floatp(
    string $description = '',
    ?float $default = null,
    ?float $minimum = null,
    ?float $maximum = null,
    array $accept = [],
): FloatParameterInterface {
    $parameter = new FloatParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($minimum !== null) {
        $parameter = $parameter->withMinimum($minimum);
    }
    if ($maximum !== null) {
        $parameter = $parameter->withMaximum($maximum);
    }
    if ($accept !== []) {
        $parameter = $parameter->withAccept(...$accept);
    }

    return $parameter;
}

/**
 * @param int[] $accept
 */
function integerp(
    string $description = '',
    ?int $default = null,
    ?int $minimum = null,
    ?int $maximum = null,
    array $accept = [],
): IntegerParameterInterface {
    $parameter = new IntegerParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($minimum !== null) {
        $parameter = $parameter->withMinimum($minimum);
    }
    if ($maximum !== null) {
        $parameter = $parameter->withMaximum($maximum);
    }
    if ($accept !== []) {
        $parameter = $parameter->withAccept(...$accept);
    }

    return $parameter;
}

function stringp(
    string $regex = '',
    string $description = '',
    ?string $default = null,
): StringParameterInterface {
    $parameter = new StringParameter($description);
    if ($regex !== '') {
        $parameter = $parameter->withRegex(new Regex($regex));
    }
    if ($default) {
        $parameter = $parameter->withDefault($default);
    }

    return $parameter;
}

function enump(string ...$string): StringParameterInterface
{
    $cases = implode('|', $string);
    $regex = "/^{$cases}\$/";

    return stringp($regex);
}

/**
 * Parameter for `YYYY-MM-DD` strings.
 */
function datep(
    string $description = '',
    ?string $default = null
): StringParameterInterface {
    $regex = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';

    return stringp($regex, $description, $default);
}

/**
 * Parameter for `hh:mm:ss` strings.
 */
function timep(
    string $description = '',
    ?string $default = null
): StringParameterInterface {
    $regex = '/^\d{2,3}:[0-5][0-9]:[0-5][0-9]$/';

    return stringp($regex, $description, $default);
}

/**
 * Parameter for `YYYY-MM-DD hh:mm:ss` strings.
 */
function datetimep(
    string $description = '',
    ?string $default = null
): StringParameterInterface {
    $regex = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])\s{1}\d{2,3}:[0-5][0-9]:[0-5][0-9]$/';

    return stringp($regex, $description, $default);
}

function objectp(
    string $className,
    string $description = '',
): ObjectParameterInterface {
    $parameter = new ObjectParameter($description);

    return $parameter->withClassName($className);
}

function filep(
    string $description = '',
    ?StringParameterInterface $name = null,
    ?StringParameterInterface $type = null,
    ?StringParameterInterface $tmp_name = null,
    ?IntegerParameterInterface $size = null,
): FileParameterInterface {
    return new FileParameter(
        name: $name ?? stringp(),
        size: $size ?? integerp(),
        type: $type ?? stringp(),
        tmp_name: $tmp_name ?? stringp(),
        description: $description,
    );
}

/**
 * @param ParameterInterface $V Generic value parameter
 * @param ParameterInterface|null $K Generic key parameter
 */
function genericp(
    ParameterInterface $V,
    ?ParameterInterface $K = null,
    string $description = '',
): GenericParameterInterface {
    if ($K === null) {
        $K = integerp();
    }

    return new GenericParameter($V, $K, $description);
}

function unionp(
    ParameterInterface ...$parameter
): UnionParameterInterface {
    $parameters = parameters(...$parameter);

    return new UnionParameter($parameters);
}

function parameters(
    ParameterInterface ...$required,
): ParametersInterface {
    return new Parameters(...$required);
}

/**
 * @param array<int|string, mixed> $arguments
 */
function arguments(
    ParametersInterface $parameters,
    array $arguments
): ArgumentsInterface {
    return new Arguments($parameters, $arguments);
}

function assertBoolean(
    BooleanParameterInterface $parameter,
    bool $argument
): bool {
    return $argument;
}

function assertString(
    StringParameterInterface $parameter,
    string $argument,
): string {
    $regex = $parameter->regex();
    if ($regex->match($argument) !== []) {
        return $argument;
    }

    throw new InvalidArgumentException(
        message("Argument value provided %provided% doesn't match the regex %regex%")
            ->withCode('%provided%', $argument)
            ->withCode('%regex%', strval($regex))
    );
}

function assertNumeric(
    IntegerParameterInterface|FloatParameterInterface $parameter,
    int|float $argument,
): int|float {
    $accept = $parameter->accept();
    if ($accept !== []) {
        if (in_array($argument, $accept, true)) {
            return $argument;
        }

        throw new InvalidArgumentException(
            message('Argument value provided %provided% is not an accepted value %value%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%value%', implode(',', $accept))
        );
    }
    $minimum = $parameter->minimum();
    if ($argument < $minimum) {
        throw new InvalidArgumentException(
            message('Argument value provided %provided% is less than %minimum%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%minimum%', strval($minimum))
        );
    }
    $maximum = $parameter->maximum();
    if ($argument > $maximum) {
        throw new InvalidArgumentException(
            message('Argument value provided %provided% is greater than %maximum%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%maximum%', strval($maximum))
        );
    }

    return $argument;
}

function assertInteger(
    IntegerParameterInterface $parameter,
    int $argument,
): int {
    assertNumeric($parameter, $argument);

    return $argument;
}

function assertFloat(
    FloatParameterInterface $parameter,
    float $argument
): float {
    assertNumeric($parameter, $argument);

    return $argument;
}

function assertNull(NullParameterInterface $parameter, mixed $argument): mixed
{
    if ($argument === null) {
        return $argument;
    }

    throw new TypeError(
        message('Argument value provided is not of type null')
    );
}

function assertObject(
    ObjectParameterInterface $parameter,
    object $argument
): object {
    if ($parameter->type()->validate($argument)) {
        return $argument;
    }

    throw new InvalidArgumentException(
        message('Argument value provided is not of type %type%')
            ->withCode('%type%', $parameter->className())
    );
}

/**
 * @param array<int|string, mixed> $argument
 * @return array<int|string, mixed> Asserted array, with fixed optional values.
 */
function assertArray(
    ArrayParameterInterface $parameter,
    array $argument,
): array {
    return arguments($parameter->parameters(), $argument)->toArray();
}

function assertNotEmpty(ParameterInterface $expected, mixed $value): void
{
    if ($expected instanceof ArrayTypeParameterInterface
        && empty($value)
        && $expected->parameters()->requiredKeys() !== []
    ) {
        throw new InvalidArgumentException(
            message('Argument value provided is empty')
        );
    }
}

/**
 * @param iterable<mixed, mixed> $argument
 * @return iterable<mixed, mixed>
 */
function assertGeneric(
    GenericParameterInterface $parameter,
    iterable $argument,
): iterable {
    $generic = ' *generic';
    $genericKey = '_K' . $generic;
    $genericValue = '_V' . $generic;
    $expected = $parameter->value();
    assertNotEmpty($expected, $argument);

    try {
        foreach ($argument as $key => $value) {
            assertNamedArgument($genericKey, $parameter->key(), $key);
            assertNamedArgument($genericValue, $parameter->value(), $value);
        }
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            getThrowableArrayErrorMessage($e->getMessage())
        );
    }

    return $argument;
}

function getThrowableArrayErrorMessage(string $message): MessageInterface
{
    $strstr = strstr($message, ':', false);
    if (! is_string($strstr)) {
        $strstr = $message; // @codeCoverageIgnore
    } else {
        $strstr = substr($strstr, 2);
    }

    return message($strstr);
}

function assertUnion(
    UnionParameterInterface $parameter,
    mixed $argument,
): mixed {
    $types = [];
    foreach ($parameter->parameters() as $parameter) {
        try {
            assertNamedArgument('', $parameter, $argument);

            return $argument;
        } catch (Throwable $e) {
            $types[] = $parameter::class;
        }
    }

    throw new InvalidArgumentException(
        message("Argument provided doesn't match the union type %type%")
            ->withCode('%type%', implode(',', $types))
    );
}

function assertNamedArgument(
    string $name,
    ParameterInterface $parameter,
    mixed $argument
): void {
    $parameters = parameters(
        ...[
            $name => $parameter,
        ]
    );
    $arguments = [
        $name => $argument,
    ];

    try {
        arguments($parameters, $arguments);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            message('Argument [%name%]: %message%')
                ->withTranslate('%name%', $name)
                ->withTranslate('%message%', $e->getMessage())
        );
    }
}

function assertArgument(ParameterInterface $parameter, mixed $argument): mixed
{
    return match (true) {
        $parameter instanceof ArrayParameterInterface
        // @phpstan-ignore-next-line
        => assertArray($parameter, $argument),
        $parameter instanceof BooleanParameterInterface
        // @phpstan-ignore-next-line
        => assertBoolean($parameter, $argument),
        $parameter instanceof FloatParameterInterface
        // @phpstan-ignore-next-line
        => assertFloat($parameter, $argument),
        $parameter instanceof GenericParameterInterface
        // @phpstan-ignore-next-line
        => assertGeneric($parameter, $argument),
        $parameter instanceof IntegerParameterInterface
        // @phpstan-ignore-next-line
        => assertInteger($parameter, $argument),
        $parameter instanceof ObjectParameterInterface
        // @phpstan-ignore-next-line
        => assertObject($parameter, $argument),
        $parameter instanceof StringParameterInterface
        // @phpstan-ignore-next-line
        => assertString($parameter, $argument),
        $parameter instanceof UnionParameterInterface
        => assertUnion($parameter, $argument),
        $parameter instanceof NullParameterInterface
        => assertNull($parameter, $argument),
        default => null,
    };
}
