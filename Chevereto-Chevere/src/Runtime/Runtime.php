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

namespace Chevere\Runtime;

use DateTimeZone;
use RuntimeException;
use Chevere\Data;
use Chevere\Message;

/**
 * Runtime applies runtime config and provide data about the App Runtime.
 */
final class Runtime
{
    /** @var Config */
    private $config;

    /** @var Data */
    public $data;

    public function __construct(Config $config)
    {
        $this->data = new Data();
        $this->config = $config;
        $this->runConfig($config);
    }

    private function runConfig(): self
    {
        foreach ($this->config->data as $k => $v) {
            if ($v === $this->data->getKey($k)) {
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
        $this->data->setKey(Config::LOCALE, $locale);

        return $this;
    }

    public function setDefaultCharset(string $charset): self
    {
        if (!@ini_set('default_charset', $charset)) {
            throw new RuntimeException(
                (new Message('Unable to set %s %v.'))
                    ->code('%s', 'default_charset')
                    ->code('%v', $charset)
                    ->toString()
            );
        }
        $this->data->setKey(Config::DEFAULT_CHARSET, $charset);

        return $this;
    }

    public function setErrorHandler(callable $errorHandler = null, int $errorTypes = null): self
    {
        if (null == $errorHandler) {
            return $this->restoreErrorHandler();
        }
        // $types = $errorTypes ?? E_ALL ^ E_NOTICE;
        set_error_handler($errorHandler);
        $this->data->setKey(Config::ERROR_HANDLER, $errorHandler);
        $this->data->setKey(Config::ERROR_REPORTING_LEVEL, error_reporting());

        return $this;
    }

    public function restoreErrorHandler(): self
    {
        restore_error_handler();
        $errorHandler = set_error_handler(function () { });
        restore_error_handler();
        $this->data->setKey(Config::ERROR_HANDLER, $errorHandler);
        $this->data->setKey(Config::ERROR_REPORTING_LEVEL, error_reporting());

        return $this;
    }

    public function setExceptionHandler(callable $exceptionHandler = null): self
    {
        if (null == $exceptionHandler) {
            return $this->restoreExceptionHandler();
        }
        set_exception_handler($exceptionHandler);
        $this->data->setKey(Config::EXCEPTION_HANDLER, $exceptionHandler);

        return $this;
    }

    public function restoreExceptionHandler(): self
    {
        restore_exception_handler();
        $handler = set_exception_handler(function () { });
        restore_exception_handler();
        $this->data->setKey(Config::EXCEPTION_HANDLER, $handler);

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
        $this->data->setKey(Config::TIMEZONE, $tzg);

        return $this;
    }

    public function setTimeZone(string $timeZone): self
    {
        date_default_timezone_set($timeZone);
        $this->data->setKey(Config::TIMEZONE, $timeZone);

        return $this;
    }

    public function setUriScheme(string $scheme): self
    {
        $this->data->setKey(Config::URI_SCHEME, $scheme);

        return $this;
    }

    public function setDebug(int $debugLevel): self
    {
        $this->data->setKey(Config::DEBUG, $debugLevel);

        return $this;
    }

    public function getRuntimeConfig(): Config
    {
        return $this->config;
    }
}
