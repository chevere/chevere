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

use Chevereto\Core\CoreException;

use Exception;
use DateTimeZone;

/**
 * Runtime applies runtime changes and provide information about the system runtime.
 */
class Runtime extends Data
{
    public function __construct()
    {
        // $this->setDataKey('OS', PHP_OS);
    }
    public function setLocale(string $locale) : self
    {
        setlocale(LC_ALL, $locale);
        $this->setDataKey('locale', $locale);
        return $this;
    }
    public function setDefaultCharset(string $charset) : self
    {
        @ini_set('default_charset', $charset);
        $this->setDataKey('defaultCharset', $charset);
        return $this;
    }
    public function setErrorHandler(callable $errorHandler, int $errorTypes = null) : self
    {
        $types = $errorTypes ?? E_ALL ^ E_NOTICE;
        set_error_handler($errorHandler, $types);
        $this->setDataKey('errorHandler', $errorHandler);
        $this->setDataKey('errorHandler.types', $types);
        return $this;
    }
    public function setExceptionHandler(string $exceptionHandler = null) : self
    {
        set_exception_handler($exceptionHandler);
        $this->setDataKey('exceptionHandler', $exceptionHandler);
        return $this;
    }
    /**
     * Fix timezone issues
     *
     * Tries to fix common bad configuration issues related to timezone.
     */
    public function fixTimeZone() : self
    {
        $tzg = @date_default_timezone_get();
        $tzs = @date_default_timezone_set($tzg);
        $utcId = DateTimeZone::listIdentifiers(DateTimeZone::UTC);
        if (false == $tzs && false == @date_default_timezone_set($utcId[0])) { // No UTC? My gosh....
            trigger_error("Invalid timezone identifier '$tzg'. Configure your PHP installation with a valid timezone identifier http://php.net/manual/en/timezones.php", E_USER_ERROR);
        }
        $this->setDataKey('timezone', $tzg);
        return $this;
    }
    /**
     * Checks if the server is running under Windows.
     *
     * @return bool TRUE if server runs on Windows.
     */
    // public static function isWindowsOs() : bool
    // {
    //     return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ?: false);
    // }
}
class RuntimeException extends CoreException
{
}
