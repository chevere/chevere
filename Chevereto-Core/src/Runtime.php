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
    public function __construct(Config $config = null)
    {
        parent::__construct();
        if (null != $config) {
            $this->runConfig($config);
        }
    }
    public function runConfig(Config $config) : self
    {
        foreach ($config->getData() as $k => $v) {
            $fnName = 'set' . ucwords($k);
            if (method_exists($this, $fnName)) {
                $this->{$fnName}($v);
            }
        }
        return $this;
    }
    public function setLocale(string $locale) : self
    {
        setlocale(LC_ALL, $locale);
        $this->setDataKey(Config::LOCALE, $locale);
        return $this;
    }
    public function setDefaultCharset(string $charset) : self
    {
        @ini_set('default_charset', $charset);
        $this->setDataKey(Config::DEFAULT_CHARSET, $charset);
        return $this;
    }
    public function setErrorHandler(callable $errorHandler = null, int $errorTypes = null) : self
    {
        if (null == $errorHandler) {
            return $this->restoreErrorHandler();
        }
        $types = $errorTypes ?? E_ALL ^ E_NOTICE;
        set_error_handler($errorHandler, $types);
        $this->setDataKey(Config::ERROR_HANDLER, $errorHandler);
        $this->setDataKey(Config::ERROR_REPORTING_LEVEL, $types);
        return $this;
    }
    public function restoreErrorHandler() : self
    {
        restore_error_handler();
        $errorHandler = set_error_handler(function () {
        });
        restore_error_handler();
        $this->setDataKey(Config::ERROR_HANDLER, $errorHandler);
        $this->setDataKey(Config::ERROR_REPORTING_LEVEL, error_reporting());
        return $this;
    }
    public function setExceptionHandler(callable $exceptionHandler = null) : self
    {
        if (null == $exceptionHandler) {
            return $this->restoreExceptionHandler();
        }
        set_exception_handler($exceptionHandler);
        $this->setDataKey(Config::EXCEPTION_HANDLER, $exceptionHandler);
        return $this;
    }
    public function restoreExceptionHandler() : self
    {
        restore_exception_handler();
        $handler = set_exception_handler(function () {
        });
        restore_exception_handler();
        $this->setDataKey(Config::EXCEPTION_HANDLER, $handler);
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
        $this->setDataKey(Config::TIMEZONE, $tzg);
        return $this;
    }
    public function setDefaultTimeZone(string $timeZone) : self
    {
        date_default_timezone_set($timeZone);
        $this->setDataKey(Config::TIMEZONE, $timeZone);
    }
    public function setUriScheme(string $scheme) : self
    {
        $this->setDataKey(Config::URI_SCHEME, $scheme);
        return $this;
    }
    public function setDebug(int $debugLevel) : self
    {
        $this->setDataKey(Config::DEBUG, $debugLevel);
        return $this;
    }
}
class RuntimeException extends CoreException
{
}
