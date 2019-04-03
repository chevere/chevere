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

use DateTimeZone;
use RuntimeException;

/**
 * Runtime applies runtime changes and provide information about the App Runtime.
 */
class Runtime extends Data
{
    public function __construct(RuntimeConfig $config = null)
    {
        parent::__construct();
        if (null != $config) {
            $this->runConfig($config);
        }
    }

    public function runConfig(RuntimeConfig $config): self
    {
        foreach ($config->getData() as $k => $v) {
            if ($v === $this->getDataKey($k)) {
                continue;
            }
            $fnName = 'set'.ucwords($k);
            if (method_exists($this, $fnName)) {
                $this->{$fnName}($v);
            }
        }

        return $this;
    }

    public function setLocale(string $locale): self
    {
        setlocale(LC_ALL, $locale);
        $this->setDataKey(RuntimeConfig::LOCALE, $locale);

        return $this;
    }

    public function setDefaultCharset(string $charset): self
    {
        if (!@ini_set('default_charset', $charset)) {
            throw new RuntimeException(
                (string) (new Message('Unable to set %s %v.'))
                    ->code('%s', 'default_charset')
                    ->code('%v', $charset)
            );
        }
        $this->setDataKey(RuntimeConfig::DEFAULT_CHARSET, $charset);

        return $this;
    }

    public function setErrorHandler(callable $errorHandler = null, int $errorTypes = null): self
    {
        if (null == $errorHandler) {
            return $this->restoreErrorHandler();
        }
        // $types = $errorTypes ?? E_ALL ^ E_NOTICE;
        set_error_handler($errorHandler);
        $this->setDataKey(RuntimeConfig::ERROR_HANDLER, $errorHandler);
        $this->setDataKey(RuntimeConfig::ERROR_REPORTING_LEVEL, error_reporting());

        return $this;
    }

    public function restoreErrorHandler(): self
    {
        restore_error_handler();
        $errorHandler = set_error_handler(function () {
        });
        restore_error_handler();
        $this->setDataKey(RuntimeConfig::ERROR_HANDLER, $errorHandler);
        $this->setDataKey(RuntimeConfig::ERROR_REPORTING_LEVEL, error_reporting());

        return $this;
    }

    public function setExceptionHandler(callable $exceptionHandler = null): self
    {
        if (null == $exceptionHandler) {
            return $this->restoreExceptionHandler();
        }
        set_exception_handler($exceptionHandler);
        $this->setDataKey(RuntimeConfig::EXCEPTION_HANDLER, $exceptionHandler);

        return $this;
    }

    public function restoreExceptionHandler(): self
    {
        restore_exception_handler();
        $handler = set_exception_handler(function () {
        });
        restore_exception_handler();
        $this->setDataKey(RuntimeConfig::EXCEPTION_HANDLER, $handler);

        return $this;
    }

    /**
     * Fix timezone issues.
     *
     * Tries to fix common bad configuration issues related to timezone.
     */
    public function fixTimeZone(): self
    {
        $tzg = @date_default_timezone_get();
        $tzs = @date_default_timezone_set($tzg);
        $utcId = DateTimeZone::listIdentifiers(DateTimeZone::UTC);
        if (!$tzs && !@date_default_timezone_set($utcId[0])) { // No UTC? My gosh....
            trigger_error("Invalid timezone identifier '$tzg'. Configure your PHP installation with a valid timezone identifier http://php.net/manual/en/timezones.php", E_USER_ERROR);
        }
        $this->setDataKey(RuntimeConfig::TIMEZONE, $tzg);

        return $this;
    }

    public function setTimeZone(string $timeZone): self
    {
        date_default_timezone_set($timeZone);
        $this->setDataKey(RuntimeConfig::TIMEZONE, $timeZone);

        return $this;
    }

    public function setUriScheme(string $scheme): self
    {
        $this->setDataKey(RuntimeConfig::URI_SCHEME, $scheme);

        return $this;
    }

    public function setDebug(int $debugLevel): self
    {
        $this->setDataKey(RuntimeConfig::DEBUG, $debugLevel);

        return $this;
    }
}
