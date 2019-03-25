<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

// FIXME: Use Symfony validator, detect missing curl extension.

use Exception;
use DateInterval;

class Validate
{
    /**
     * Check for valid timezone.
     *
     * @param string $timezone timezone id
     *
     * @return bool TRUE if $timezone is a valid timezone
     */
    public static function timezone(string $timezone): bool
    {
        if ($timezone == '') {
            false;
        }
        $valid = [];
        $list = timezone_abbreviations_list();
        foreach ($list as $zone) {
            foreach ($zone as $item) {
                $valid[$item['timezone_id']] = true;
            }
        }

        return isset($valid[$timezone]);
    }

    /**
     * Check if the given date interval is valid or not.
     *
     * @param string $dateinterval date interval
     *
     * @return bool TRUE if the date interval is valid
     */
    public static function dateInterval(string $dateinterval): bool
    {
        try {
            new DateInterval($dateinterval);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Finds if a variable is a real email address.
     *
     * This function checks if an email address is valid and if the email
     * address is real or not.
     *
     * @param string $email email address to check
     *
     * @return bool TRUE if the email address is valid
     */
    public static function emailReal(string $email): bool
    {
        $valid = true;
        $atIndex = strrpos($email, '@');
        if ($atIndex === false) {
            $valid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $valid = false;
            } elseif ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $valid = false;
            } elseif ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $valid = false;
            } elseif (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $valid = false;
            } elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $valid = false;
            } elseif (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $valid = false;
            } elseif (!preg_match(
                '/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                str_replace('\\\\', '', $local)
            )) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace('\\\\', '', $local))) {
                    $valid = false;
                }
            }
            if ($valid && !(checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'))) {
                // domain not found in DNS
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Validate HEX color.
     *
     * @param string $color color to test, like #FF0000
     *
     * @return bool TRUE if $color is a valid HEX color
     */
    public static function colorHEX(string $color): bool
    {
        $val = ltrim($color, '#');

        return (bool) preg_match('/^([a-f0-9]{3}){1,2}$/i', $val);
    }

    /**
     * Validate an IP address.
     *
     * @param string $ip IP address to check
     *
     * @return bool TRUE if $ip is a valid IPV4 or IPV6 address
     */
    public static function ipAddress(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Checks if a string type is an integer using regex.
     *
     * @param string $string value to be checked
     *
     * @return bool TRUE if the value is an integer
     */
    public static function integer(string $string): bool
    {
        return !preg_match('/\D/', $string);
    }

    /**
     * Checks if a variable looks like an URL.
     *
     * @param string $url URL being evaluated
     *
     * @return bool TRUE if the variable looks like an URL
     */
    public static function url(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) { // Note: This doesn't validate if the URL contains foreign chars
            return true;
        }
        if (strlen($url) !== strlen(utf8_encode($url))) {
            $parsedUrl = parse_url($url);
            if (!is_countable($parsedUrl) || count($parsedUrl) < 2) { // At least scheme and host
                return false;
            }
            $schemes = ['http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn', 'tel', 'fax', 'xmpp'];
            $scheme = $parsedUrl['scheme'] ?? null;
            if ($scheme == null || !in_array(strtolower($scheme), $schemes) || !isset($parsedUrl['host'])) { // Must be a valid scheme
                return false;
            }
            // At this point, this thing looks like an URL
            return true;
        }

        return false;
    }

    /**
     * Checks if an URL is valid and real using cURL.
     *
     * @param string $url URL being evaluated
     *
     * @return bool TRUE if the variable is an real and valid URL
     */
    public static function realUrl(string $url): bool
    {
        return false;
        // TODO: Migrate to Guzzle
        // if (static::url($url) == false) {
        //     return false;
        // }
        // $res = false;
        // if (function_exists('curl_init')) {
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_NOBODY, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        //     curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        //     $res = @curl_exec($ch);
        //     curl_close($ch);
        // } elseif (ini_get('allow_url_fopen')) {
        //     $res = file_get_contents($url);
        // }
        // return $res != false;
    }

    /**
     * Checks HTTPS URL string.
     *
     * @param string $url URL
     *
     * @return bool TRUE if $url protocol is HTTPS
     */
    public static function HttpsUrl(string $url): bool
    {
        return Utils\Str::startsWith('https://', $url);
    }

    /**
     * Checks if a regular expression pattern is valid.
     *
     * @param string $regex regular expresion pattern
     *
     * @return bool TRUE if $regex is a valid regular expression
     */
    public static function regex(string $regex): bool
    {
        set_error_handler(function () {
        }, E_WARNING);
        $return = preg_match($regex, '') !== false;
        restore_error_handler();

        return $return;
    }
}
