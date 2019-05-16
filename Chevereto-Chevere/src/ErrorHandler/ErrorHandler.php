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
use const Chevereto\Chevere\CORE_NS_HANDLE;
use const Chevereto\Chevere\App\PATH;
use Chevereto\Chevere\App;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Utils\Dump;
use Chevereto\Chevere\Utils\DumpPlain;
use Chevereto\Chevere\Utils\DateTime;
use Chevereto\Chevere\Utils\Str;
use Chevereto\Chevere\Interfaces\ErrorHandlerInterface;
use ErrorException;
use ReflectionObject;
use ReflectionProperty;
use Throwable;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * Super flat and minimal error handler utility.
 *
 * - Handles PHP errors as exceptions
 * - Provides clean and usable messages in HTML/markdown format
 * - Logs to system using Monolog
 * - Logs on a daily basis or any alternative datetime format you want to
 * - Uses UTC for everything
 * - Configurable debug output (app/config.php)
 * - CLI channel
 */
class ErrorHandler implements ErrorHandlerInterface
{
    // Customize the relative folder where logs will be stored
    const LOG_DATE_FOLDER = 'Y/m/d/';
    // null will read app/config.php. Any boolean value will override that
    const DEBUG = null;
    // null will use App\PATH_LOGS ? PATH_LOGS ? traverse
    const PATH_LOGS = ROOT_PATH . App\PATH . 'var/logs/';
    // Title with debug = false
    const NO_DEBUG_TITLE = 'Something went wrong';
    // Content with debug = false
    const NO_DEBUG_CONTENT = '<p>The system has failed and the server wasn\'t able to fulfil your request. This incident has been logged.</p><p>Please try again later and if the problem persist don\'t hesitate to contact your system administrator.</p>';
    // CSS stylesheet
    const CSS = 'html{color:#000;font:16px Helvetica,Arial,sans-serif;line-height:1.3;background:#3498db;background:-moz-linear-gradient(top,#3498db 0%,#8e44ad 100%);background:-webkit-linear-gradient(top,#3498db 0%,#8e44ad 100%);background:linear-gradient(to bottom,#3498db 0%,#8e44ad 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#3498db",endColorstr="#8e44ad",GradientType=0)}.body--block{margin:20px}.body--flex{margin:0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.user-select-none{-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}main{background:none;display:block;padding:0;margin:0;border:0;width:470px}.body--block main{margin:0 auto}@media (min-width:768px){main{padding:20px}}.main--stack{width:100%;max-width:900px}.hr{display:block;height:1px;color:transparent;background:hsl(192,15%,84%)}.hr>span{opacity:0;line-height:0}.main--stack hr:last-of-type{margin-bottom:0}.t{font-weight:700;margin-bottom:5px}.t--scream{font-size:2.25em;margin-bottom:0}.t--scream span{font-size:.667em;font-weight:400}.t--scream span::before{white-space:pre;content:"\A"}.t>.hide{display:inline-block}.c code{padding:2px 5px}.c code,.c pre{background:hsl(192,15%,95%);line-height:normal}.c pre.pre--even{background:hsl(192,15%,97%)}.c pre{overflow:auto;word-wrap:break-word;font-size:13px;font-family:Consolas,monospace,sans-serif;display:block;margin:0;padding:10px}main>div{padding:20px;background:#FFF}main>div,main>div> *{word-break:break-word;white-space:normal}@media (min-width:768px){main>div{-webkit-box-shadow:2px 2px 4px 0 rgba(0,0,0,.09);box-shadow:2px 2px 4px 0 rgba(0,0,0,.09);border-radius:2px}}main>div>:first-child{margin-top:0}main>div>:last-child{margin-bottom:0}.note{margin:1em 0}.fine-print{color:#BBB}.hide{width:0;height:0;opacity:0;overflow:hidden}
    .c pre {
        border: 1px solid hsl(192,15%,84%);
        border-bottom: 0; 
        border-top: 0;
    }';
    /**
     * Stack template.
     *
     * HTML template for each stack entry
     *
     * Available placeholders:
     * - %x% Applies even class (pre--even)
     * - %i% Stack number
     * - %f% File
     * - %l% Line
     * - %fl% File + Line
     * - %c% class
     * - %t% type (::, ->)
     * - %m% Method (function)
     * - %a% Arguments
     */
    const HTML_STACK_TEMPLATE = "<pre class=\"%x%\">#%i% %fl%\n%c%%t%%m%()%a%</pre>";
    const CONSOLE_STACK_TEMPLATE = "#%i% %fl%\n%c%%t%%m%()%a%";
    // HTML template (document)
    const HTML_TEMPLATE = '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="generator" content="Chevereto\Chevere"><style>%css%</style></head><body class="%bodyClass%">%body%</body></html>';
    const CONSOLE_TEMPLATE = '%body%';
    // HTML body for debug = false
    const HTML_BODY_NO_DEBUG_TEMPLATE = '<main><div><div class="t t--scream">%title%</div>%content%<p class="fine-print">%datetimeUtc% • %id%</p></div></main>';
    // HTML body for debug = true
    const HTML_BODY_DEBUG_TEMPLATE = '<main class="main--stack"><div>%content%<div class="c note user-select-none"><b>Note:</b> This message is being displayed because of active debug mode. Remember to turn this off when going production by editing <code>%configFilePath%</code></div></div></main>';
    const CONFIG_FILE_PATH = App\PATH . 'config.php';
    const COLUMNS = 120;
    // Line break
    const HR = '<div class="hr"><span>------------------------------------------------------------</span></div>';
    // Section keys
    const SECTION_TITLE = 'title';
    const SECTION_MESSAGE = 'message';
    const SECTION_ID = 'id';
    const SECTION_TIME = 'time';
    const SECTION_STACK = 'stack';
    const SECTION_CLIENT = 'client';
    const SECTION_REQUEST = 'request';
    const SECTION_SERVER = 'server';

    /**
     * Verbose aware console sections.
     */
    const CONSOLE_TABLE = [
        self::SECTION_TITLE => OutputInterface::VERBOSITY_QUIET,
        self::SECTION_MESSAGE => OutputInterface::VERBOSITY_QUIET,
        self::SECTION_ID => OutputInterface::VERBOSITY_NORMAL,
        self::SECTION_TIME => OutputInterface::VERBOSITY_VERBOSE,
        self::SECTION_STACK => OutputInterface::VERBOSITY_VERY_VERBOSE,
        self::SECTION_CLIENT => OutputInterface::VERBOSITY_VERBOSE,
        self::SECTION_REQUEST => false,
        self::SECTION_SERVER => false,
    ];
    /**
     * PHP error to monolog table
     * code => [monolog code, title].
     */
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
    /**
     * PHP error code LogLevel table
     * Taken from Monolog\ErrorHandler (defaultErrorLevelMap).
     */
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

    /**
     * You can bind any of these by turning $propName into %propName% in your
     * template constants. When extending, is easier to touch the constants, not
     * the properties.
     *
     * @see ErrorException::parseContentTemplate()
     */
    public $debug;
    public $plainStack;
    public $richStack;
    public $consoleStack;
    public $code;
    public $loggerLevel;
    public $message;
    public $type;
    public $body;
    public $bodyClass;
    public $file;
    public $line;
    public $class;
    public $datetimeUtc;
    public $timestamp;
    public $id;
    public $logFilename;
    public $url;
    public $requestMethod;
    public $clientIp;
    public $clientUserAgent;
    public $serverHost;
    public $serverProtocol;
    public $serverPort;
    public $serverSoftware;
    public $title;
    public $content;
    public $thrown;
    public $className;
    public $configFilePath;
    public $css;
    public $hr;

    /**
     * Protected properties won't be applied on templates.
     */
    protected $isXmlHttpRequest;
    protected $exception;
    protected $arguments;
    protected $table;
    protected $plainContent;
    protected $richContentTemplate;
    protected $plainContentTemplate;
    protected $richContentSections; // HTML + CONSOLE
    protected $plainContentSections; // TXT
    protected $output;
    protected $logDateFormat;
    protected $logger;
    protected $headers;
    protected $consoleSections;

    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct($args)
    {
        $this->setArguments($args);
        $this->setXhrConditional();
        $this->setTimeProperties();
        $this->setId();
        $this->setConfigFilepath();
        $this->setHr();
        $this->setCss();
        $this->setServer();
        $this->setDebug();
        $this->setBodyClass();
        $this->setExceptionProperties();
        $this->setLogDateFormat();
        $this->setLogFilename();
        $this->setLogger();
        $this->setStack();
        $this->setContentSections();
        $this->appendContentGlobals();
        $this->generateContentTemplate();
        $this->setContentProperties();
        $this->parseContentTemplate();
        $this->loggerWrite();
        $this->setOutput();
        $this->out();
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

    protected function setContentSections()
    {
        // Plain (txt) is the default "always do" format.
        $plain = [
            static::SECTION_TITLE => ['%title% <span>in&nbsp;%file%:%line%</span>'],
            static::SECTION_MESSAGE => ['# Message', '%message%' . ($this->code ? ' [Code #%code%]' : null)],
            static::SECTION_TIME => ['# Time', '%datetimeUtc% [%timestamp%]'],
        ];
        $plain[static::SECTION_ID] = ['# Incident ID:%id%', 'Logged at %logFilename%'];
        $plain[static::SECTION_STACK] = ['# Stack trace', '%plainStack%'];
        $plain[static::SECTION_CLIENT] = ['# Client', '%clientIp% %clientUserAgent%'];
        $plain[static::SECTION_REQUEST] = ['# Request', '%serverProtocol% %requestMethod% %url%'];
        $plain[static::SECTION_SERVER] = ['# Server', '%serverHost% (port:%serverPort%) %serverSoftware%'];

        if ('cli' == php_sapi_name()) {
            $verbosity = Console::output()->getVerbosity();
        }

        foreach ($plain as $k => $v) {
            $keyString = (string) $k;
            if ('cli' == php_sapi_name() && false == static::CONSOLE_TABLE[$k]) {
                continue;
            }
            $this->setPlainContentSection($keyString, $v);
            if (isset($verbosity)) {
                $lvl = static::CONSOLE_TABLE[$k];
                if (false === $lvl || $verbosity < $lvl) {
                    continue;
                }
                if ($k == static::SECTION_STACK) {
                    $v[1] = '%consoleStack%';
                }
                $this->setConsoleSection($keyString, $v);
            } else {
                if ($k == static::SECTION_STACK) {
                    $v[1] = '%richStack%';
                }
                $this->setRichContentSection($keyString, $v);
            }
        }
    }

    /**
     * @param string $key     content section key
     * @param array  $section section content [title, content]
     */
    protected function setPlainContentSection(string $key, array $section): void
    {
        $this->plainContentSections[$key] = $section;
    }

    /**
     * @param string $key     console section key
     * @param array  $section section content [title, <content>]
     */
    protected function setConsoleSection(string $key, array $section): void
    {
        $section = array_map(function (string $value) {
            return strip_tags(html_entity_decode($value));
        }, $section);
        $this->consoleSections[$key] = $section;
    }

    /**
     * @param string $key     content section key
     * @param array  $section section content [title, content]
     */
    protected function setRichContentSection(string $key, array $section): void
    {
        $section[0] = Str::replaceFirst('# ', '<span class="hide"># </span>', $section[0]);
        $this->richContentSections[$key] = $section;
    }

    protected function appendContentGlobals()
    {
        foreach (['GET', 'POST', 'FILES', 'COOKIE', 'SESSION', 'SERVER'] as $v) {
            $k = '_' . $v;
            $v = isset($GLOBALS[$k]) ? $GLOBALS[$k] : null;
            if ($v) {
                $this->setRichContentSection($k, ['$' . $k, $this->wrapStringHr('<pre>' . Dump::out($v) . '</pre>')]);
                $this->setPlainContentSection($k, ['$' . $k, strip_tags($this->wrapStringHr(DumpPlain::out($v)))]);
            }
        }
    }

    protected function setContentProperties()
    {
        $this->title = $this->thrown;
        $this->message = nl2br($this->message);
    }

    protected function parseContentTemplate()
    {
        $properties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $this->setTableValue($property->getName(), $property->getValue($this));
        }
        $this->content = strtr($this->richContentTemplate, $this->table);
        $this->plainContent = strtr($this->plainContentTemplate, $this->table);
        $this->setTableValue('content', $this->content);
    }

    protected function setOutput()
    {
        $this->headers = [];
        if (true == $this->isXmlHttpRequest) {
            $this->setJsonOutput();
        } else {
            if ('cli' == php_sapi_name()) {
                $this->setConsoleOutput();
            } else {
                $this->setHtmlOutput();
            }
        }
    }

    protected function setJsonOutput(): void
    {
        $json = new Json();
        $this->headers = array_merge($this->headers, Json::CONTENT_TYPE);
        $response = [static::NO_DEBUG_TITLE, 500];
        $log = [
            'id' => $this->getTableValue('id'),
            'level' => $this->loggerLevel,
            'filename' => $this->getTableValue('logFilename'),
        ];
        switch ($this->debug) {
            case 0:
                unset($log['filename']);
            break;
            case 1:
                $response[0] = $this->thrown . ' in ' . $this->getTableValue('file') . ':' . $this->getTableValue('line');
                $error = [];
                foreach (['file', 'line', 'code', 'message', 'class'] as $v) {
                    $error[$v] = $this->getTableValue($v);
                }
                $json->setDataKey('error', $error);
            break;
        }
        $json->setDataKey('log', $log);
        $json->setResponse(...$response);
        $this->output = (string) $json; // printable json string
    }

    protected function setHtmlOutput(): void
    {
        switch ($this->debug) {
            default:
            case 0:
                $this->content = static::NO_DEBUG_CONTENT;
                $this->setTableValue('content', $this->content);
                $this->setTableValue('title', static::NO_DEBUG_TITLE);
                $bodyTemplate = static::HTML_BODY_NO_DEBUG_TEMPLATE;
            break;
            case 1:
                $bodyTemplate = static::HTML_BODY_DEBUG_TEMPLATE;
            break;
        }
        $this->setTableValue('body', strtr($bodyTemplate, $this->table));
        $this->output = strtr(static::HTML_TEMPLATE, $this->table);
    }

    protected function setConsoleOutput(): void
    {
        foreach ($this->consoleSections as $k => $v) {
            if ('title' == $k) {
                $tpl = $v[0];
            } else {
                Console::io()->section(strtr($v[0], $this->table));
                $tpl = $v[1];
            }
            $message = strtr($tpl, $this->table);
            if ('title' == $k) {
                Console::io()->error($message);
            } else {
                Console::io()->writeln($message);
            }
        }
        Console::io()->writeln('');
    }

    protected function out(): void
    {
        if ($this->isXmlHttpRequest) {
            $response = new HttpJsonResponse();
        } else {
            $response = new HttpResponse();
        }
        $response->setContent($this->output);
        $response->setLastModified(new DateTime());
        $response->setStatusCode(500);
        foreach ($this->headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        $response->send();
    }

    protected function setArguments()
    {
        $this->arguments = func_get_args();
    }

    /**
     * Detects XMLHttpRequest (XHR).
     */
    protected function setXhrConditional()
    {
        // if (App::instance() && App::instance()->getRequest() instanceof App) {
        //     $this->isXmlHttpRequest = App::instance()->getRequest()->isXmlHttpRequest();
        // } else {
        //     $this->isXmlHttpRequest = Http::isXHR();
        // }
    }

    protected function setTimeProperties()
    {
        $this->datetimeUtc = DateTime::getUTC();
        $this->timestamp = @strtotime($this->datetimeUtc);
    }

    protected function setId()
    {
        $this->id = uniqid();
    }

    protected function setConfigFilepath()
    {
        $this->configFilePath = static::CONFIG_FILE_PATH;
    }

    protected function setHr()
    {
        $this->hr = static::HR;
    }

    protected function setCss()
    {
        $this->css = static::CSS;
    }

    protected function setLogDateFormat()
    {
        $this->logDateFormat = static::LOG_DATE_FOLDER;
    }

    /**
     * Both App and Chevereto\Chevere logs to app/var/logs.
     */
    protected function setLogFilename()
    {
        $path = static::PATH_LOGS;
        $path = Path::normalize($path);
        $path = rtrim($path, '/') . '/';
        $date = gmdate($this->logDateFormat, $this->timestamp);
        $this->logFilename = $path . $this->loggerLevel . '/' . $date . $this->timestamp . '_' . $this->id . '.log';
    }

    protected function setLogger()
    {
        $formatter = new LineFormatter(null, null, true, true);
        $handler = new StreamHandler($this->logFilename);
        $handler->setFormatter($formatter);
        $this->logger = new Logger(__NAMESPACE__);
        $this->logger->setTimezone(new DateTimeZone('UTC'));
        $this->logger->pushHandler($handler);
        $this->logger->pushHandler(new FirePHPHandler());
    }

    protected function setDebug()
    {
        $error_reporting = error_reporting();
        error_reporting(0);
        try {
            $debug = App::runtimeInstance()->getDataKey('debug');
        } catch (Throwable $e) { // Don't panic, such trucazo!
        }
        error_reporting($error_reporting);
        $this->debug = (bool) ($debug ?? static::DEBUG);
    }

    protected function setBodyClass()
    {
        $this->bodyClass = !headers_sent() ? 'body--flex' : 'body--block';
    }

    /**
     * @param string $text text to wrap
     *
     * @return string wrapped text
     */
    protected function wrapStringHr(string $text): string
    {
        return $this->hr . "\n" . $text . "\n" . $this->hr;
    }

    /**
     * Sets the exeption properties from the Exception object.
     */
    protected function setExceptionProperties()
    {
        $this->exception = $this->arguments[0];
        $this->className = get_class($this->exception);
        if (Str::startsWith(CORE_NS_HANDLE, $this->className)) {
            $this->className = Str::replaceFirst(CORE_NS_HANDLE, null, $this->className);
        }
        $this->thrown = $this->className . ' thrown';
        if ($this->exception instanceof ErrorException) {
            $code = $this->exception->getSeverity();
            $e_type = $code;
        } else {
            $code = $this->exception->getCode();
            $e_type = E_ERROR;
        }
        $this->code = $code;
        $this->type = static::getErrorByCode($e_type);
        $this->loggerLevel = static::getLoggerLevel($e_type) ?? 'error';
        $this->message = $this->exception->getMessage();
        $this->file = Path::normalize($this->exception->getFile());
        $this->line = (string) $this->exception->getLine();
    }

    protected function setServer()
    {
        if ('cli' == php_sapi_name()) {
            $this->clientIp = $_SERVER['argv'][0];
            $this->clientUserAgent = Console::inputString();
        } else {
            $request = App::requestInstance();
            if (null !== $request) {
                $this->url = $request->readInfoKey('requestUri') ?? 'unknown';
                $this->clientUserAgent = $request->getHeaders()->get('User-Agent');
                $this->requestMethod = $request->readInfoKey('method');
                $this->serverHost = $request->readInfoKey('host');
                $this->serverPort = $request->readInfoKey('port');
                $this->serverProtocol = $request->readInfoKey('protocolVersion');
                $this->serverSoftware = $request->getServer()->get('SERVER_SOFTWARE');
                $this->clientIp = $request->readInfoKey('clientIp');
            }
        }
    }

    protected function setStack()
    {
        $richStack = [];
        $plainStack = [];
        $consoleStack = [];
        $i = 0;
        $trace = $this->exception->getTrace();
        if ($this->exception instanceof ErrorException) {
            $this->thrown = $this->type;
            $this->message = $this->message;
            unset($trace[0]);
        }
        $stack = new Stack($trace);
        // $this->consoleStack = $stack->getConsoleStack();
        // $this->richStack = $stack->getRichStack();
        // $this->plainStack = $stack->getPlainStack();
    }

    /**
     * $table stores the template placeholders and its value.
     *
     * @param string $key   table key
     * @param mixed  $value value
     */
    protected function setTableValue(string $key, $value): void
    {
        $this->table["%$key%"] = $value;
    }

    /**
     * @param string $key table key
     */
    protected function getTableValue(string $key)
    {
        return $this->table["%$key%"] ?? null;
    }

    protected function loggerWrite()
    {
        $log = strip_tags($this->plainContent);
        $log .= "\n\n" . str_repeat('=', static::COLUMNS);
        $this->logger->log($this->loggerLevel, $log);
    }

    /**
     * @param int $code PHP error code
     *
     * @return string error type (string), null if the error code doesn't match
     *                any error type
     */
    protected static function getErrorByCode(int $code): ?string
    {
        return static::ERROR_TABLE[$code];
    }

    /**
     * @param int $code PHP error code
     *
     * @return string logger level (string), null if the error code doesn't match
     *                any error type
     */
    protected static function getLoggerLevel(int $code): ?string
    {
        return static::PHP_LOG_LEVEL[$code] ?? null;
    }

    protected function generateContentTemplate()
    {
        $sections_length = count($this->plainContentSections);
        $i = 0;
        foreach ($this->plainContentSections as $k => $plainSection) {
            $richSection = $this->richContentSections[$k] ?? null;
            $section_length = count($plainSection);
            if (0 == $i || isset($plainSection[1])) {
                $this->richContentTemplate .= '<div class="t' . (0 == $i ? ' t--scream' : null) . '">' . $richSection[0] . '</div>';
                $this->plainContentTemplate .= html_entity_decode($plainSection[0]);
                if (0 == $i) {
                    $this->richContentTemplate .= "\n" . '<div class="hide">' . str_repeat('=', static::COLUMNS) . '</div>';
                    $this->plainContentTemplate .= "\n" . str_repeat('=', static::COLUMNS);
                }
            }
            if ($i > 0) {
                $j = 1 == $section_length ? 0 : 1;
                for ($j; $j < $section_length; ++$j) {
                    if ($section_length > 1) {
                        $this->richContentTemplate .= "\n";
                        $this->plainContentTemplate .= "\n";
                    }
                    $this->richContentTemplate .= '<div class="c">' . $richSection[$j] . '</div>';
                    $this->plainContentTemplate .= $plainSection[$j];
                }
            }
            if ($i + 1 < $sections_length) {
                $this->richContentTemplate .= "\n" . '<br>' . "\n";
                $this->plainContentTemplate .= "\n\n";
            }
            ++$i;
        }
    }
}
