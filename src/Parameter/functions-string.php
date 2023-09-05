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

use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Regex\Regex;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Stringable;
use function Chevere\Message\message;

function string(
    string $regex = '',
    string $description = '',
    ?string $default = null,
): StringParameterInterface {
    $parameter = new StringParameter($description);
    if ($regex !== '') {
        $parameter = $parameter->withRegex(new Regex($regex));
    }
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }

    return $parameter;
}

function integerString(
    string $description = '',
    ?string $default = null,
): StringParameterInterface {
    return string(
        regex: '/^\d+$/',
        description: $description,
        default: $default
    );
}

function booleanString(
    string $description = '',
    ?string $default = null,
): StringParameterInterface {
    return string(
        regex: '/^[01]$/',
        description: $description,
        default: $default
    );
}

function enum(string ...$string): StringParameterInterface
{
    if ($string === []) {
        throw new InvalidArgumentException(
            message('At least one string must be provided')
        );
    }
    $cases = implode('|', $string);
    $regex = "/^{$cases}\$/";

    return string($regex);
}

/**
 * Parameter for `YYYY-MM-DD` strings.
 */
function date(
    string $description = 'YYYY-MM-DD',
    ?string $default = null
): StringParameterInterface {
    $regex = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';

    return string($regex, $description, $default);
}

/**
 * Parameter for `hh:mm:ss` strings.
 */
function time(
    string $description = 'hh:mm:ss',
    ?string $default = null
): StringParameterInterface {
    $regex = '/^\d{2,3}:[0-5][0-9]:[0-5][0-9]$/';

    return string($regex, $description, $default);
}

/**
 * Parameter for `YYYY-MM-DD hh:mm:ss` strings.
 */
function datetime(
    string $description = 'YYYY-MM-DD hh:mm:ss',
    ?string $default = null
): StringParameterInterface {
    $regex = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])\s{1}\d{2,3}:[0-5][0-9]:[0-5][0-9]$/';

    return string($regex, $description, $default);
}

function assertString(
    StringParameterInterface $parameter,
    Stringable|string $argument,
): string {
    $regex = $parameter->regex();
    $argument = strval($argument);
    if ($regex->match($argument) !== []) {
        return $argument;
    }

    throw new InvalidArgumentException(
        message("Argument value provided %provided% doesn't match the regex %regex%")
            ->withCode('%provided%', $argument)
            ->withCode('%regex%', strval($regex))
    );
}
