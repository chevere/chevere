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

namespace Chevere\ExceptionHandler;

use const Chevere\APP_PATH;


use DateTime;
use DateTimeZone;
use TypeError;
use Chevere\App\Builder;
use Chevere\Http\ServerRequest;
use Chevere\Data\Data;
use Chevere\Path\Path;
use Chevere\Runtime\Runtime;
use Chevere\ExceptionHandler\src\Formatter;
use Chevere\ExceptionHandler\src\Output;
use Chevere\ExceptionHandler\src\Style;
use Chevere\ExceptionHandler\src\Template;
use Chevere\ExceptionHandler\src\Wrap;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Data\Traits\DataMethodTrait;

/**
 * The Chevere exception handler.
 */
final class ExceptionHandler
{
    use DataMethodTrait;

    /** @var string Relative folder where logs will be stored */
    const LOG_DATE_FOLDER_FORMAT = 'Y/m/d/';

    /** @var ?bool Null will read app/config.php. Any boolean value will override that */
    const DEBUG = null;

    /** @var string Null will use App\PATH_LOGS ? PATH_LOGS ? traverse */
    const PATH_LOGS = APP_PATH . 'var/logs/';

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

    /** @var RequestContract The detected/forged HTTP request */
    private $request;

    /** @var bool */
    private $isDebugEnabled;

    /** @var string */
    private $loggerLevel;

    /** @var Wrap */
    private $wrap;

    /** @var string */
    private $logDateFolderFormat;

    private $logger;

    /** @var Runtime */
    private $runtime;

    /** @var array Contains all the loaded configuration files (App) */
    // private $loadedConfigFiles;

    /** @var Output */
    private $output;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(...$args)
    {
        $this->data = new Data();
        $this->setTimeProperties();
        $this->data = $this->data
            ->withKey('id', uniqid('', true));
        try {
            $this->request = Builder::requestInstance();
        } catch (TypeError $e) {
            $this->request = ServerRequest::fromGlobals();
        }
        $this->runtime = Builder::runtime();
        $this->isDebugEnabled = (bool) $this->runtime->data()->key('debug');

        $this->logDateFolderFormat = static::LOG_DATE_FOLDER_FORMAT;
        $this->wrap = new Wrap($args[0]);
        $this->loggerLevel = $this->wrap->data()->key('loggerLevel');
        $this->setLogFilePathProperties();
        $this->setLogger();

        $formatter = new Formatter($this);
        $formatter = $formatter
            ->withLineBreak(Template::BOX_BREAK_HTML)
            ->withCss(Style::CSS);

        $this->output = new Output($this, $formatter);
        $this->loggerWrite();
        $this->output->out();
    }

    public function isDebugEnabled(): bool
    {
        return $this->isDebugEnabled;
    }

    public function request(): RequestContract
    {
        return $this->request;
    }

    public function wrap(): Wrap
    {
        return $this->wrap;
    }

    public static function exception($e): void
    {
        new static($e);
    }

    private function setTimeProperties(): void
    {
        $dt = new DateTime('now', new DateTimeZone('UTC'));
        $dateTimeAtom = $dt->format(DateTime::ATOM);
        $this->data = $this->data
            ->withMergedArray([
                'dateTimeAtom' => $dateTimeAtom,
                'timestamp' => strtotime($dateTimeAtom),
            ]);
    }

    private function setLogFilePathProperties(): void
    {
        $path = Path::normalize(static::PATH_LOGS);
        $path = rtrim($path, '/') . '/';
        $date = gmdate($this->logDateFolderFormat, $this->data->key('timestamp'));
        $id = $this->data->key('id');
        $timestamp = $this->data->key('timestamp');
        $logFilename = $path . $this->loggerLevel . '/' . $date . $timestamp . '_' . $id . '.log';
        $this->data = $this->data
            ->withKey('logFilename', $logFilename);
    }

    private function setLogger(): void
    {
        $lineFormatter = new LineFormatter(null, null, true, true);
        $logFilename = $this->data->key('logFilename');
        $streamHandler = new StreamHandler($logFilename);
        $streamHandler->setFormatter($lineFormatter);
        $this->logger = new Logger(__NAMESPACE__);
        $this->logger->setTimezone(new DateTimeZone('UTC'));
        $this->logger->pushHandler($streamHandler);
        $this->logger->pushHandler(new FirePHPHandler());
    }

    private function loggerWrite(): void
    {
        $log = strip_tags($this->output->textPlain());
        $log .= "\n\n" . str_repeat('=', Formatter::COLUMNS);
        $this->logger->log($this->loggerLevel, $log);
    }
}
