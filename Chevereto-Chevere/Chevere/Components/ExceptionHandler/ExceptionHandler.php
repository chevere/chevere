<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\ExceptionHandler;

use DateTime;
use DateTimeZone;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

use Chevere\Components\App\Instances\RequestInstance;
use Chevere\Components\App\Instances\RuntimeInstance;
use Chevere\Components\Data\Data;
use Chevere\Components\Data\Traits\DataMethodTrait;
use Chevere\Components\ExceptionHandler\src\Formatter;
use Chevere\Components\ExceptionHandler\src\Output;
use Chevere\Components\ExceptionHandler\src\Style;
use Chevere\Components\ExceptionHandler\src\Template;
use Chevere\Components\ExceptionHandler\src\Wrap;
use Chevere\Components\Path\Path;
use Chevere\Components\Runtime\Runtime;
use Chevere\Contracts\Http\RequestContract;

use const Chevere\APP_PATH;

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

    /** @var string */
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

    private RequestContract $request;

    private bool $isDebugEnabled;

    private string $loggerLevel;

    private Wrap $wrap;

    /** @var string */
    private string $logDateFolderFormat;

    private Logger $logger;

    private Runtime $runtime;

    /** @var array Contains all the loaded configuration files (App) */
    // private $loadedConfigFiles;

    private Output $output;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct(...$args)
    {
        $this->data = new Data([]);
        $this->setTimeProperties();
        $this->data = $this->data
            ->withAddedKey('id', uniqid('', true));
        $this->request = RequestInstance::get();
        $this->runtime = RuntimeInstance::get();
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

    public static function exception($exception): void
    {
        new static($exception);
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
        $absolute = (new Path('var/logs/'))->absolute();
        $date = gmdate($this->logDateFolderFormat, $this->data->key('timestamp'));
        $id = $this->data->key('id');
        $timestamp = $this->data->key('timestamp');
        $logFilename = $absolute . $this->loggerLevel . '/' . $date . $timestamp . '_' . $id . '.log';
        $this->data = $this->data
            ->withAddedKey('logFilename', $logFilename);
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
