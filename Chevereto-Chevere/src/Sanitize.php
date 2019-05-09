<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere;

use Exception;

class Sanitize
{
    const TO_LOWERCASE = 'TO_LOWERCASE';
    const STRIP_NON_ALNUM = 'STRIP_NON_ALNUM';
    const STRIP_TAGS = 'STRIP_TAGS';
    const STRIP_WHITESPACE = 'STRIP_WHITESPACE';
    const STRIP_EXTRA_WHITESPACE = 'STRIP_EXTRA_WHITESPACE';
    const TRUNCATE = 'TRUNCATE';
    const TRUNCATE_PAD = 'TRUNCATE_PAD';
    const CONVERT_HTML = 'CONVERT_HTML';
    const CONVERT_HTML_FUNCTION = 'CONVERT_HTML_FUNCTION';
    const STRING_DEFAULT_OPTIONS = [
        self::CONVERT_HTML => true,
        self::CONVERT_HTML_FUNCTION => 'htmlspecialchars',
        self::TO_LOWERCASE => false,
        self::STRIP_NON_ALNUM => false,
        self::STRIP_TAGS => true,
        self::STRIP_WHITESPACE => false,
        self::STRIP_EXTRA_WHITESPACE => true,
        self::TRUNCATE => null,
        self::TRUNCATE_PAD => '...',
    ];

    /**
     * Removes all whitespaces from a string.
     *
     * @param string $string string to be cleaned
     *
     * @return string string with no whitespaces
     */
    public static function removeWhitespaces(string $string): string
    {
        return str_replace(' ', '', $string) ?? '';
    }

    /**
     * Sanitizes double or any extra slashes from a path.
     *
     * @param string $path path to be sanitized
     *
     * @return string sanitized path forward and back slashes
     */
    public static function pathSlashes(string $path): string
    {
        return preg_replace('#/+#', '/', $path) ?? '';
    }

    /**
     * Sanitizes a path.
     *
     * Performs path cleaning removing all extra slashes
     *
     * @param string $path path to be sanitized
     *
     * @return string sanitized path
     */
    public static function path(string $path): string
    {
        $clean = static::pathSlashes($path);

        return rtrim($clean, '/').'/';
    }

    /**
     * Returns a sanitized string, typically for URLs.
     *
     * @param string $string  string to be sanitized
     * @param array  $options Options array []
     *                        Sanitize::CONVERT_HTML TRUE to convert HTML.
     *                        Sanitize::CONVERT_HTML_FUNCTION Callable function to convert HTML.
     *                        Sanitize::TO_LOWERCASE TRUE lowercase output.
     *                        Sanitize::STRIP_NON_ALNUM TRUE to trim any non-alphanumeric character.
     *                        Sanitize::STRIP_TAGS TRUE use strip_tags.
     *                        Sanitize::STRIP_EXTRA_WHITESPACE TRUE to strip extra whitespaces.
     *                        Sanitize::TRUNCATE Integer value for text truncate (maximum chars).
     *						  Sanitize::TRUNCATE_PAD Pad (...) used for truncate.

     *
     * @return string a sanitized string
     */
    public static function string(string $string, array $options = []): ?string
    {
        $options = array_merge(static::STRING_DEFAULT_OPTIONS, $options);
        $filters = [
            static::STRIP_WHITESPACE => Core::namespaced('Utils\Str::stripWhitespace'),
            static::CONVERT_HTML => [$options[static::CONVERT_HTML_FUNCTION], ENT_QUOTES, 'UTF-8'],
            static::TO_LOWERCASE => Core::namespaced('Utils\Str::toLowercase'),
            static::STRIP_NON_ALNUM => Core::namespaced('Utils\Str::stripNonAlnum'),
            static::STRIP_TAGS => 'strip_tags',
            static::STRIP_EXTRA_WHITESPACE => Core::namespaced('Utils\Str::stripExtraWhitespace'),
            static::TRUNCATE => [Core::namespaced('Utils\Str::truncate'), $options[static::TRUNCATE], $options[static::TRUNCATE_PAD]],
        ];
        $return = $string;
        foreach ($options as $k => $v) {
            if (isset($filters[$k]) && $v == true) {
                $filter = $filters[$k];
                if (is_array($filter)) {
                    array_splice($filter, 1, 0, $return);
                    $args = $filter;
                } else {
                    $args = [$filter, $return];
                }
                if (!is_callable($args[0])) {
                    throw new Exception(
                        (new Message('Invalid callable %s'))->code('%s', (string) $args[0])
                    );
                }
                $return = call_user_func(...$args);
            }
        }

        return $return;
    }

    /**
     * Generates safe HTML output.
     *
     * @param mixed $var  HTML code to be handled. It can be a string or an array
     *                    containing properties with HTML code.
     * @param int   $flag flag to be used in htmlspecialchars()
     *
     * @return string safe HTML string
     */
    // TODO: Add safeHTMLArray method, keep safeHTML for strings.
    public static function safeHTML($var, $flag = ENT_QUOTES): ?string
    {
        if (!is_array($var)) {
            return $var === null ? null : htmlspecialchars($var, $flag, 'UTF-8'); // htmlspecialchars keeps ñ, á and all the UTF-8 valid chars
        }
        $safe = [];
        foreach ($var as $k => $v) {
            $call = __METHOD__;
            $safe[$k] = $call($v, $flag);
        }

        return null;
        // return $safe;
    }
}
