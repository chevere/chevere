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

namespace Chevereto\Chevere\ErrorHandler;

use const Chevereto\Chevere\ROOT_PATH;
use const Chevereto\Chevere\App\PATH;
use Chevereto\Chevere\App;
use Chevereto\Chevere\HttpRequest;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Runtime;
// use Chevereto\Chevere\Utils\DateTime;
use Chevereto\Chevere\Interfaces\ErrorHandlerInterface;
use DateTime;
use ErrorException;
use DateTimeZone;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * The Chevere ErrorHandler.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /** @var string Relative folder where logs will be stored */
    const LOG_DATE_FOLDER_FORMAT = 'Y/m/d/';

    /** @var ?bool Null will read app/config.php. Any boolean value will override that */
    const DEBUG = null;

    /** @var string Null will use App\PATH_LOGS ? PATH_LOGS ? traverse */
    const PATH_LOGS = ROOT_PATH.App\PATH.'var/logs/';

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

    public $body;
    public $class;
    public $datetimeUtc;
    public $timestamp;
    public $logFilename;

    // public $loadedConfigFilesString;

    /** @var array Arguments passed to the static exception handler */
    public $arguments;

    /** @var string */
    protected $logDateFolderFormat;

    protected $logger;

    /** @var HttpRequest The detected/forged HTTP request */
    public $httpRequest;

    /** @var Runtime */
    protected $runtimeInstance;

    /** @var DateTime Created in construct (asap) */
    protected $dateTime;

    /** @var string Unique id (uniqid) for the handled error */
    public $id;

    /** @var array Contains all the loaded configuration files (App) */
    protected $loadedConfigFiles;

    /** @var string A string representation of $loadedConfigFiles */
    public $loadedConfigFilesString;

    /** @var bool True, debug enabled */
    public $isDebugEnabled;

    /** @var array */
    protected $args;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(...$args)
    {
        $this->setTimeProperties('now');
        $this->id = uniqid('', true);
        // $this->arguments = $args;
        $this->handleHttpRequest(App::requestInstance());
        $this->runtimeInstance = App::runtimeInstance();
        $this->isDebugEnabled = (bool) $this->runtimeInstance->getDataKey('debug');
        $this->setloadedConfigFiles($this->runtimeInstance->getRuntimeConfig()->getLoadedFiles());
        $this->logDateFolderFormat = static::LOG_DATE_FOLDER_FORMAT;

        $exceptionHandler = new ExceptionHandler($args[0]);

        $this->loggerLevel = $exceptionHandler->loggerLevel;
        $this->setLogFilePathProperties(static::PATH_LOGS, );
        $this->setLogger(__NAMESPACE__);

        $formatter = new Formatter($this, $exceptionHandler);
        $formatter->setLineBreak(Template::BOX_BREAK_HTML);
        $formatter->setCss(Style::CSS);

        $output = new Output($this, $formatter);

        $this->loggerWrite($output->plainContent);

        $output->out();
    }

    protected function setTimeProperties(string $time): void
    {
        $this->dateTime = new DateTime($time, new DateTimeZone('UTC'));
        $this->datetimeUtc = $this->dateTime->format(DateTime::ATOM);
        $this->timestamp = strtotime($this->datetimeUtc);
    }

    protected function handleHttpRequest(?HttpRequest $httpRequest): void
    {
        if ($httpRequest) {
            $this->httpRequest = $httpRequest;
        }
    }

    protected function setloadedConfigFiles(array $loadedConfigFiles)
    {
        $this->loadedConfigFiles = $loadedConfigFiles;
        $this->loadedConfigFilesString = implode(';', $this->loadedConfigFiles);
    }

    protected function setLogFilePathProperties(string $basePath)
    {
        $path = Path::normalize($basePath);
        $path = rtrim($path, '/').'/';
        $date = gmdate($this->logDateFolderFormat, $this->timestamp);
        $this->logFilename = $path.$this->loggerLevel.'/'.$date.$this->timestamp.'_'.$this->id.'.log';
    }

    // TODO: Refactor: pass logger as object
    protected function setLogger(string $name)
    {
        $lineFormatter = new LineFormatter(null, null, true, true);
        $streamHandler = (new StreamHandler($this->logFilename))->setFormatter($lineFormatter);
        $this->logger = new Logger($name);
        $this->logger->setTimezone(new DateTimeZone('UTC'));
        $this->logger->pushHandler($streamHandler);
        $this->logger->pushHandler(new FirePHPHandler());
    }

    protected function loggerWrite(string $plainContent)
    {
        $log = strip_tags($plainContent);
        $log .= "\n\n".str_repeat('=', Formatter::COLUMNS);
        $this->logger->log($this->loggerLevel, $log);
    }

    protected static function exceptionHandler(): void
    {
        new static(...func_get_args());
    }

    public static function error($severity, $message, $file, $line): void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function exception($e): void
    {
        static::exceptionHandler(...func_get_args());
    }

    /**
     * @param int $code PHP error code
     *
     * @return string error type (string), null if the error code doesn't match
     *                any error type
     */
    // TODO: Add to interface
    public static function getErrorByCode(int $code): ?string
    {
        return static::ERROR_TABLE[$code];
    }

    /**
     * @param int $code PHP error code
     *
     * @return string logger level (string), null if the error code doesn't match
     *                any error type
     */
    // TODO: Add to interface
    public static function getLoggerLevel(int $code): ?string
    {
        return static::PHP_LOG_LEVEL[$code] ?? null;
    }
}
