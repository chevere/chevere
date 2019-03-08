<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Exception;
use DateTimeZone;

class Runtime
{
    const DEFAULT_LOCALE = 'en_US.UTF8';
    const DEFAULT_CHARSET = 'utf-8';
    /**
     * Set default charset.
     */
    public static function setDefaultCharset() : void
    {
        setlocale(LC_ALL, static::DEFAULT_LOCALE);
        @ini_set('default_charset', static::DEFAULT_CHARSET);
    }
    /**
     * Register own error handler for exceptions and errors.
     */
    public static function registerErrorHandler() : void
    {
        set_exception_handler('Chevereto\Core\ErrorHandler::exception');
        set_error_handler('Chevereto\Core\ErrorHandler::error', E_ALL ^ E_NOTICE);
    }
    /**
     * Fix timezone issues
     *
     * Tries to fix common bad configuration issues related to timezone.
     */
    public static function fixTimeZone() : void
    {
        $tzg = @date_default_timezone_get();
        $tzs = @date_default_timezone_set($tzg);
        $utcId = DateTimeZone::listIdentifiers(DateTimeZone::UTC);
        if (!$tzs && !@date_default_timezone_set($utcId[0])) { // No UTC? My gosh....
            trigger_error("Invalid timezone identifier '$tzg'. Configure your PHP installation with a valid timezone identifier http://php.net/manual/en/timezones.php", E_USER_ERROR);
        }
    }
    /**
     * Checks if the server is running under Windows.
     *
     * @return bool TRUE if server runs on Windows.
     */
    public function isWindowsOs() : bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ?: false);
    }
}