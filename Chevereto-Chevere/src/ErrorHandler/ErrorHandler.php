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

namespace Chevere\ErrorHandler;

use DateTime;
use ErrorException;
use DateTimeZone;
use Throwable;
use const Chevere\ROOT_PATH;
use const Chevere\App\PATH as AppPath;
use Chevere\HttpFoundation\Request;
use Chevere\App\Loader;
use Chevere\Path;
use Chevere\Runtime\Runtime;
use Chevere\ErrorHandler\src\Formatter;
use Chevere\ErrorHandler\src\Output;
use Chevere\ErrorHandler\src\Style;
use Chevere\ErrorHandler\src\Template;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

// use Chevere\Contracts\ErrorHandler\ErrorHandlerContract;

/**
 * The Chevere ErrorHandler.
 */
final class ErrorHandler
{
    /** @var string Relative folder where logs will be stored */
    const LOG_DATE_FOLDER_FORMAT = 'Y/m/d/';

    /** @var ?bool Null will read app/config.php. Any boolean value will override that */
    const DEBUG = null;

    /** @var string Null will use App\PATH_LOGS ? PATH_LOGS ? traverse */
    const PATH_LOGS = ROOT_PATH.AppPath.'var/logs/';

    /** Readable PHP error mapping */
    const ERROR_TABLE = [
        E_ERROR => 'Fatal error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core error',
        E_CORE_WARNING => 'Core warning',
        E_COMPILE_ERROR => 'Compile error',
        E_COMPILE_WARNING => 'Compile warning',
        E_USER_ERROR => 'Fatal error',
        E_USER_WARNING => 'Warning',
        E_USER_NOTICE => 'Notice',
        E_STRICT => 'Strict standars',
        E_RECOVERABLE_ERROR => 'Recoverable error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'Deprecated',
    ];

    /** PHP error code LogLevel table. Taken from Monolog\ErrorHandler (defaultErrorLevelMap). */
    const PHP_LOG_LEVEL = [
        E_ERROR => LogLevel::CRITICAL,
        E_WARNING => LogLevel::WARNING,
        E_PARSE => LogLevel::ALERT,
        E_NOTICE => LogLevel::NOTICE,
        E_CORE_ERROR => LogLevel::CRITICAL,
        E_CORE_WARNING => LogLevel::WARNING,
        E_COMPILE_ERROR => LogLevel::ALERT,
        E_COMPILE_WARNING => LogLevel::WARNING,
        E_USER_ERROR => LogLevel::ERROR,
        E_USER_WARNING => LogLevel::WARNING,
        E_USER_NOTICE => LogLevel::NOTICE,
        E_STRICT => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED => LogLevel::NOTICE,
        E_USER_DEPRECATED => LogLevel::NOTICE,
    ];

    /** @var int */
    private $timestamp;

    /** @var string */
    private $utcDateTimeAtom;

    /** @var string */
    private $logFilename;

    /** @var Request The detected/forged HTTP request */
    private $request;

    /** @var string A string representation of $loadedConfigFiles */
    private $loadedConfigFilesString;

    /** @var bool */
    private $isDebugEnabled;

    /** @var string Unique id (uniqid) for the handled error */
    private $id;

    /** @var string */
    private $body;

    /** @var string */
    private $class;

    /** @var string */
    private $arguments;

    /** @var string */
    private $loggerLevel;

    /** @var string */
    private $logDateFolderFormat;

    private $logger;

    /** @var Runtime */
    private $runtimeInstance;

    /** @var DateTime Created in construct (asap) */
    private $dateTime;

    /** @var array Contains all the loaded configuration files (App) */
    private $loadedConfigFiles;

    /** @var Output */
    private $output;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(...$args)
    {
        $this->setTimeProperties();
        $this->id = uniqid('', true);
        // $this->arguments = $args;
        try {
            $request = Loader::request();
        } catch (Throwable $e) {
            $request = Request::createFromGlobals();
        }
        $this->request = $request;
        $this->runtimeInstance = Loader::runtime();
        $this->isDebugEnabled = (bool) $this->runtimeInstance->data->getKey('debug');
        $this->setloadedConfigFiles();
        $this->logDateFolderFormat = static::LOG_DATE_FOLDER_FORMAT;

        $exceptionHandler = new ExceptionHandler($args[0]);

        $this->loggerLevel = $exceptionHandler->loggerLevel;
        $this->setLogFilePathProperties(static::PATH_LOGS, );
        $this->setLogger();

        $formatter = new Formatter($this, $exceptionHandler);
        $formatter->setLineBreak(Template::BOX_BREAK_HTML);
        $formatter->setCss(Style::CSS);

        $this->output = new Output($this, $formatter);
        $this->loggerWrite();
        $this->output->out();
    }

    public function timestamp(): int
    {
        return $this->timestamp;
    }

    public function dateTimeAtom(): string
    {
        return $this->dateTimeAtom;
    }

    public function logFilename(): string
    {
        return $this->logFilename;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function loadedConfigFilesString(): string
    {
        return $this->loadedConfigFilesString;
    }

    public function isDebugEnabled(): bool
    {
        return $this->isDebugEnabled;
    }

    public function id(): string
    {
        return $this->id;
    }

    public static function error($severity, $message, $file, $line): void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function exception($e): void
    {
        static::exceptionHandler(...func_get_args());
    }

    private function setTimeProperties(): void
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        $this->dateTimeAtom = $dt->format(DateTime::ATOM);
        $this->timestamp = strtotime($this->dateTimeAtom);
    }

    private function setloadedConfigFiles()
    {
        $this->loadedConfigFiles = $this->runtimeInstance->getRuntimeConfig()->getLoadedFilepaths();
        $this->loadedConfigFilesString = implode(';', $this->loadedConfigFiles);
    }

    private function setLogFilePathProperties(string $basePath): void
    {
        $path = Path::normalize($basePath);
        $path = rtrim($path, '/').'/';
        $date = gmdate($this->logDateFolderFormat, $this->timestamp);
        $this->logFilename = $path.$this->loggerLevel.'/'.$date.$this->timestamp.'_'.$this->id.'.log';
    }

    private function setLogger(): void
    {
        $lineFormatter = new LineFormatter(null, null, true, true);
        $streamHandler = (new StreamHandler($this->logFilename))->setFormatter($lineFormatter);
        $this->logger = new Logger(__NAMESPACE__);
        $this->logger->setTimezone(new DateTimeZone('UTC'));
        $this->logger->pushHandler($streamHandler);
        $this->logger->pushHandler(new FirePHPHandler());
    }

    private function loggerWrite()
    {
        $log = strip_tags($this->output->plainContent);
        $log .= "\n\n".str_repeat('=', Formatter::COLUMNS);
        $this->logger->log($this->loggerLevel, $log);
    }

    private static function exceptionHandler(): void
    {
        new static(...func_get_args());
    }
}
