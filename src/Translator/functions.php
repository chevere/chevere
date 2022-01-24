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

namespace Chevere\Translator;

// @codeCoverageIgnoreStart

use Chevere\Throwable\Exceptions\LogicException;
use Gettext\Translator;
use Gettext\TranslatorInterface;

function getTranslator(): TranslatorInterface
{
    try {
        return TranslatorInstance::get();
    } catch (LogicException $e) {
        return new Translator();
    }
}

/**
 * Translates a string.
 */
function __(string $message)
{
    return getTranslator()->gettext($message);
}
/**
 * Translates a formatted string with `sprintf`.
 */
function __f(string $message, ...$arguments)
{
    return sprintf(__($message), ...$arguments);
}
/**
 * Translates a formatted string with `strtr`.
 */
function __t(string $message, array $fromTo = [])
{
    return strtr(__($message), $fromTo);
}
/**
 * Translates a formatted plural string.
 */
function __n(string $singular, string $plural, int $count)
{
    return getTranslator()->ngettext($singular, $plural, $count);
}
/**
 * Translates a formatted plural string with `sprintf`.
 */
function __nf(string $singular, string $plural, int $count, ...$arguments)
{
    return sprintf(
        __n($singular, $plural, $count),
        ...$arguments
    );
}
/**
 * Translates a formatted plural string with `strtr`.
 */
function __nt(string $singular, string $plural, int $count, array $fromTo)
{
    return strtr(__n($singular, $plural, $count), $fromTo);
}

// @codeCoverageIgnoreEnd
