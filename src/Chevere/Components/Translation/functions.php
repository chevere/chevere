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

namespace Chevere\Components\Translation {
    use Chevere\Exceptions\Core\LogicException;
    use Gettext\Translator;

    function getTranslator(): Translator
    {
        try {
            return TranslatorInstance::get();
        } catch (LogicException $e) {
            return new Translator();
        }
    }
}

namespace {
use function Chevere\Components\Translation\getTranslator;

    if (function_exists('_s') === false) {
        /**
         * Alias for `gettext`.
         */
        function _s(string $message)
        {
            return getTranslator()->gettext($message);
        }
    }
    if (function_exists('_sf') === false) {
        /**
         * Alias for `gettext` with `sprintf` handling.
         */
        function _sf(string $message, ...$arguments)
        {
            return sprintf(_s($message), ...$arguments);
        }
    }
    if (function_exists('_st') === false) {
        /**
         * Alias for `gettext` with `strtr` handling.
         */
        function _st(string $message, array $fromTo = [])
        {
            return strtr(_s($message), $fromTo);
        }
    }
    if (function_exists('_n') === false) {
        /**
         * Alias for `ngettext`.
         */
        function _n(string $singular, string $plural, int $count)
        {
            return getTranslator()->ngettext($singular, $plural, $count);
        }
    }
    if (function_exists('_nf') === false) {
        /**
         * Alias for `ngettext` with `sprintf` handling.
         */
        function _nf(string $singular, string $plural, int $count, ...$arguments)
        {
            return sprintf(
                _n($singular, $plural, $count, ...$arguments),
                ...$arguments
            );
        }
    }
    if (function_exists('_nt') === false) {
        /**
         * Alias for `ngettext` with `strtr` handling.
         */
        function _nt(string $singular, string $plural, int $count, array $fromTo)
        {
            return strtr(_n($singular, $plural, $count, $fromTo), $fromTo);
        }
    }
}

// @codeCoverageIgnoreEnd
