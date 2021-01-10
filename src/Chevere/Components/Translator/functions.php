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

// @codeCoverageIgnoreStart

namespace Chevere\Components\Translator {
    use Chevere\Exceptions\Core\LogicException;
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
}

namespace {
    use function Chevere\Components\Translator\getTranslator;

    if (function_exists('__') === false) {
        /**
         * Translates a string.
         */
        function __(string $message)
        {
            return getTranslator()->gettext($message);
        }
    }
    if (function_exists('__f') === false) {
        /**
         * Translates a formatted string with `sprintf`.
         */
        function __f(string $message, ...$arguments)
        {
            return sprintf(__($message), ...$arguments);
        }
    }
    if (function_exists('__t') === false) {
        /**
         * Translates a formatted string with `strtr`.
         */
        function __t(string $message, array $fromTo = [])
        {
            return strtr(__($message), $fromTo);
        }
    }
    if (function_exists('__n') === false) {
        /**
         * Translates a formatted plural string.
         */
        function __n(string $singular, string $plural, int $count)
        {
            return getTranslator()->ngettext($singular, $plural, $count);
        }
    }
    if (function_exists('__nf') === false) {
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
    }
    if (function_exists('__nt') === false) {
        /**
         * Translates a formatted plural string with `strtr`.
         */
        function __nt(string $singular, string $plural, int $count, array $fromTo)
        {
            return strtr(__n($singular, $plural, $count), $fromTo);
        }
    }
}

// @codeCoverageIgnoreEnd
