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

use ErrorException;
use ReflectionObject;
use ReflectionProperty;
use DateTimeZone;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

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
 *
 * Psst, edit the constants when extending.
 */
class ErrorHandler
{
    // Use path separator to customize how logs will be stored
    const DEFAULT_LOG_DATE_FORMAT = 'Y/m/d/';
    // null will read app/config.php. Any boolean value will override that
    const DEBUG = null;
    // null will use App\PATH_LOGS ? PATH_LOGS ? traverse
    const PATH_LOGS = null;
    // Title with debug = false
    const NO_DEBUG_TITLE = 'Something went wrong';
    // Content with debug = false
    const NO_DEBUG_CONTENT = '<p>The system has failed and the server wasn\'t able to fulfil your request. This incident has been logged.</p><p>Please try again later and if the problem persist don\'t hesitate to contact your system administrator.</p>';
    // CSS stylesheet
    const CSS = 'html{color:#000;font:16px Helvetica,Arial,sans-serif;line-height:1.3;background:#3498db;background:-moz-linear-gradient(top,#3498db 0%,#8e44ad 100%);background:-webkit-linear-gradient(top,#3498db 0%,#8e44ad 100%);background:linear-gradient(to bottom,#3498db 0%,#8e44ad 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#3498db",endColorstr="#8e44ad",GradientType=0)}.body--block{margin:20px}.body--flex{margin:0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.user-select-none{-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}main{background:none;display:block;padding:0;margin:0;border:0;width:470px}.body--block main{margin:0 auto}@media (min-width:768px){main{padding:20px}}.main--stack{width:100%;max-width:900px}.hr{display:block;height:1px;color:transparent;background:hsl(192,15%,84%)}.hr>span{opacity:0;line-height:0}.main--stack hr:last-of-type{margin-bottom:0}.t{font-weight:700;margin-bottom:5px}.t--scream{font-size:2.25em;margin-bottom:0}.t--scream span{font-size:.667em;font-weight:400}.t--scream span::before{white-space:pre;content:"\A"}.t>.hide{display:inline-block}.c code{padding:2px 5px}.c code,.c pre{background:hsl(192,15%,95%);line-height:normal}.c pre.pre--even{background:hsl(192,15%,97%)}.c pre{overflow:auto;word-wrap:break-word;font-family:Consolas,monospace,sans-serif;display:block;margin:0;padding:10px}main>div{padding:20px;background:#FFF}main>div,main>div> *{word-break:break-word;white-space:normal}@media (min-width:768px){main>div{-webkit-box-shadow:2px 2px 4px 0 rgba(0,0,0,.09);box-shadow:2px 2px 4px 0 rgba(0,0,0,.09);border-radius:2px}}main>div>:first-child{margin-top:0}main>div>:last-child{margin-bottom:0}.note{margin:1em 0}.fine-print{color:#BBB}.hide{width:0;height:0;opacity:0}
    .c pre {
        border: 1px solid hsl(192,15%,84%);
        border-bottom: 0; 
        border-top: 0;
    }';
    /**
     * Stack template
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
    const HTML_TEMPLATE = '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="generator" content="Chevereto\Core"><style>%css%</style></head><body class="%bodyClass%">%body%</body></html>';
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
     * Verbose aware console sections
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
     * code => [monolog code, title]
     */
    const ERROR_TABLE = [
        E_ERROR             => 'Fatal error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parse error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core error',
        E_CORE_WARNING      => 'Core warning',
        E_COMPILE_ERROR     => 'Compile error',
        E_COMPILE_WARNING   => 'Compile warning',
        E_USER_ERROR        => 'Fatal error',
        E_USER_WARNING      => 'Warning',
        E_USER_NOTICE       => 'Notice',
        E_STRICT            => 'Strict standars',
        E_RECOVERABLE_ERROR => 'Recoverable error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'Deprecated',
    ];
    /**
     * PHP error code LogLevel table
     * Taken from Monolog\ErrorHandler (defaultErrorLevelMap)
     */
    const PHP_LOG_LEVEL  = [
        E_ERROR             => LogLevel::CRITICAL,
        E_WARNING           => LogLevel::WARNING,
        E_PARSE             => LogLevel::ALERT,
        E_NOTICE            => LogLevel::NOTICE,
        E_CORE_ERROR        => LogLevel::CRITICAL,
        E_CORE_WARNING      => LogLevel::WARNING,
        E_COMPILE_ERROR     => LogLevel::ALERT,
        E_COMPILE_WARNING   => LogLevel::WARNING,
        E_USER_ERROR        => LogLevel::ERROR,
        E_USER_WARNING      => LogLevel::WARNING,
        E_USER_NOTICE       => LogLevel::NOTICE,
        E_STRICT            => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED        => LogLevel::NOTICE,
        E_USER_DEPRECATED   => LogLevel::NOTICE,
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
    public $clientRemoteAddress;
    public $clientUserAgent;
    public $serverName;
    public $serverProtocol;
    public $serverPort;
    public $serverSoftware;
    public $title;
    public $content;
    public $thrown;
    public $className;
    public $configFilePath; // CONFIG_FILE_PATH
    public $css; // CSS
    public $hr; // HR

    /**
     * Protected properties won't be applied on templates
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
     * Exception handler
     *
     * Turns exceptions into printable messages, both HTML and error log.
     *
     * This method
     */
    protected static function exceptionHandler()
    {
        (new static())
            ->setArguments(...func_get_args())
            ->setXhrConditional()
            ->setSignatureProperties()
            ->setConstantProperties()
            ->setServerProperties()
            ->setDebug()
            ->setBodyClass()
            ->setExceptionProperties()
            ->setLogProperties()
            ->setLogger()
            ->setStack()
            ->setContentSections()
            ->appendContentGlobals()
            ->generateContentTemplate()
            ->setContentProperties()
            ->parseContentTemplate()
            ->loggerExec()
            ->setOutput()
            ->out();
    }
    /**
     * Detects XMLHttpRequest (XHR)
     */
    protected function setXhrConditional() : self
    {
        // if (App::instance() && App::instance()->getRequest() instanceof App) {
        //     $this->isXmlHttpRequest = App::instance()->getRequest()->isXmlHttpRequest();
        // } else {
        //     $this->isXmlHttpRequest = Http::isXHR();
        // }
        return $this;
    }
    /**
     * Handle HTTP status
     *
     * Sets HTTP status code 500 (if possible). Done here to issue HTTP 500 on
     * own class errors (syntax and whatnot).
     *
     * Later, Response will set the real HTTP 500 code.
     */
    // protected static function handleHttpStatus() : void
    // {
    //     if (headers_sent() == false) {
    //         Http::setStatusCode(500);
    //     }
    // }
    /**
     * Sets signature properties (time + id)
     *
     * Assign properties used to identify the error incident.
     */
    protected function setSignatureProperties() : self
    {
        $this->datetimeUtc = Utils\DateTime::getUTC();
        $this->timestamp = @strtotime($this->datetimeUtc);
        $this->id = uniqid();
        return $this;
    }
    /**
     * Sets constant properties from const values
     */
    protected function setConstantProperties() : self
    {
        // Set these directly from const
        $this->configFilePath = static::CONFIG_FILE_PATH;
        $this->css = static::CSS;
        $this->hr = static::HR;
        return $this;
    }
    protected function setLogProperties() : self
    {
        $this->setLogDateFormat();
        $this->setLogFilename();
        return $this;
    }
    /**
     * Set debug flag from const DEBUG > Config
     *
     * If debug is enabled, all the error information will be printed.
     * If disabled, it will hide the error content.
     * In any case, this class should always logs everything into the error log.
     */
    protected function setLogDateFormat(?string $format = null) : void
    {
        $this->logDateFormat = $format ?? (Config::has('logDateFormat') ? Config::get('logDateFormat') : static::DEFAULT_LOG_DATE_FORMAT);
    }
    /**
     * Sets log filename
     *
     * Both App and Chevereto\Core logs to the same place.
     */
    protected function setLogFilename(?string $path = null) : void
    {
        if ($path == null) {
            if (static::PATH_LOGS == null) {
                $path = defined('App\PATH_LOGS') ? App\PATH_LOGS : (defined('PATH_LOGS') ? PATH_LOGS : (PATH . App::LOGS . '/'));
            } else {
                $path = static::PATH_LOGS;
            }
        }
        $path = Path::normalize($path);
        $path = rtrim($path, '/') . '/';
        $date = gmdate($this->logDateFormat, $this->timestamp);
        $this->logFilename = $path . $this->loggerLevel . '/' . $date . $this->loggerLevel . '.log';
    }
    protected function setLogger() : self
    {
        $formatter = new LineFormatter(null, null, true, true);
        $handler = new StreamHandler($this->logFilename);
        $handler->setFormatter($formatter);
        $this->logger = new Logger(__NAMESPACE__);
        $this->logger->setTimezone(new DateTimeZone('UTC'));
        $this->logger->pushHandler($handler);
        $this->logger->pushHandler(new FirePHPHandler());
        return $this;
    }
    /**
     * Set debug flag from const DEBUG > Config
     *
     * If debug is enabled, all the error information will be printed.
     * If disabled, it will hide the error content.
     * In any case, this class should always logs everything into the error log.
     */
    protected function setDebug() : self
    {
        if (static::DEBUG === null) { // Set it from config
            $debug = Config::has('debug') ? (bool) Config::get('debug') : false;
        } else { // Set it from static
            $debug = static::DEBUG;
        }
        $this->debug = $debug;
        return $this;
    }
    
    /**
     * Sets body class based on headers sent
     *
     * If headers have been sent, it will use display:block instead of
     * display:flex.
     */
    protected function setBodyClass() : self
    {
        $this->bodyClass = headers_sent() == false ? 'body--flex' : 'body--block';
        return $this;
    }
    /**
     * Wraps text with hr
     *
     * @param string $text Text to wrap.
     * @return string Wrapped text.
     */
    protected function wrapTextHr(string $text) : string
    {
        return $this->hr . "\n" . $text . "\n" . $this->hr;
    }
    /**
     * Set arguments
     *
     * When called, the exception arguments are passed directly to this method.
     *
     * @see exceptionHandler
     */
    protected function setArguments() : self
    {
        $this->arguments = func_get_args();
        return $this;
    }
    /**
     * Sets the exeption properties from the Exception object
     */
    protected function setExceptionProperties() : self
    {
        $this->exception = $this->arguments[0];
        $this->className = get_class($this->exception);
        if (Utils\Str::startsWith(CORE_NS_HANDLE, $this->className)) {
            // Get rid of own namespace
            $this->className = Utils\Str::replaceFirst(CORE_NS_HANDLE, null, $this->className);
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
        $this->line = Path::normalize((string) $this->exception->getLine());
        return $this;
    }
    /**
     * Sets the server properties from $_SERVER.
     */
    protected function setServerProperties() : self
    {
        // $this->url = Http::requestUrl();
        $map = [
            'serverPort'        => 'SERVER_PORT',
            'clientUserAgent'   => 'HTTP_USER_AGENT',
            'requestMethod'     => 'REQUEST_METHOD',
            'serverName'        => 'SERVER_NAME',
            'serverProtocol'    => 'SERVER_PROTOCOL',
            'serverSoftware'    => 'SERVER_SOFTWARE',
        ];
        $app = App::instance();

        if ($app->hasRequest()) {
            $request = $app->getRequest();
            $this->clientRemoteAddress = $request->getClientIp();
            foreach ($map as $k => $v) {
                $this->{$k} = $request->server->get($v);
            }
        } else {
            if (php_sapi_name() == 'cli') {
                $this->clientRemoteAddress = $_SERVER['argv'][0];
                $this->clientUserAgent = Console::getInputString();
            } else {
                $this->clientRemoteAddress = Http::clientIp();
                foreach ($map as $k => $v) {
                    $this->{$k} = $_SERVER[$v];
                }
            }
        }
        
        return $this;
    }
    /**
     * Generate JSON response.
     */
    protected function setJsonOutput() : void
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
                $json->addData('error', $error);
            break;
        }
        $json->addData('log', $log);
        $json->setResponse(...$response);
        $this->output = (string) $json; // printable json string
    }
    /**
     * Set stacks from exception trace. This class is CLI aware.
     */
    protected function setStack() : self
    {
        $anonClass = 'class@anonymous';
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
        foreach ($trace as $frame) {
            $plainArgs = [];
            $richArgs = [];
            $plainArgsString = null;
            $richArgsString = null;
            if (isset($frame['args']) && is_array($frame['args'])) {
                foreach ($frame['args'] as $k => $v) {
                    $aux = 'Arg#' . ($k+1) . ' ';
                    $plainArgs[] = $aux . Utils\DumpPlain::out($v);
                    $richArgs[] = $aux . Utils\Dump::out($v);
                }
                if ($plainArgs) {
                    $plainArgsString = "\n" . implode("\n", $plainArgs);
                    $richArgsString = "\n" . implode("\n", $richArgs);
                }
            }
            if (isset($frame['class']) && Utils\Str::startsWith($anonClass, $frame['class'])) {
                $frameFile = Utils\Str::replaceFirst($anonClass, null, $frame['class']);
                $frame['file'] = substr($frameFile, 0, strpos($frameFile, '.php') + 4);
                $frame['class'] = $anonClass;
                $frame['line'] = null;
            }
            if ($frame['function'] == Core::namespaced('autoloader')) {
                $frame['file'] = $frame['file'] ?? (PATH . 'autoloader.php');
            }
            $plainTable = [
                '%x%' => ($i & 1) ? 'pre--even' : null,
                '%i%' => $i,
                '%f%' => $frame['file'] ?? null,
                '%l%' => $frame['line'] ?? null,
                '%fl%' => isset($frame['file']) ? ($frame['file'] . ':' . $frame['line']) : null,
                '%c%' => $frame['class'] ?? null,
                '%t%' => $frame['type'] ?? null,
                '%m%' => $frame['function'],
                '%a%' => $plainArgsString,
            ];
            $richTable = $plainTable;
            array_pop($richTable);
            // Dump types map
            foreach ([
                    '%f%' => Utils\Dump::_FILE,
                    '%l%' => Utils\Dump::_FILE,
                    '%fl%' => Utils\Dump::_FILE,
                    '%c%' => Utils\Dump::_CLASS,
                    '%t%' => Utils\Dump::_OPERATOR,
                    '%m%' => Utils\Dump::_FUNCTION,
                ] as $k => $v) {
                $richTable[$k] = isset($plainTable[$k]) ? Utils\Dump::wrap($v, $plainTable[$k]) : null;
            }
            $richTable['%a%'] = $richArgsString;
            if (php_sapi_name() == 'cli') {
                $consoleStack[] = strtr(static::CONSOLE_STACK_TEMPLATE, $richTable);
            }
            $plainStack[] = strtr(static::HTML_STACK_TEMPLATE, $plainTable);
            $richStack[] = strtr(static::HTML_STACK_TEMPLATE, $richTable);
            $i++;
        }
        $glue = "\n" . $this->hr . "\n";
        $this->consoleStack = strip_tags(implode($glue, $consoleStack));
        $this->richStack = $this->wrapTextHr(implode($glue, $richStack));
        $this->plainStack = $this->wrapTextHr(implode($glue, $plainStack));
        return $this;
    }
    /**
     * Set rich content section.
     *
     * @param string $key Content section key.
     * @param array $section Section content [title, content].
     */
    protected function setRichContentSection(string $key, array $section) : void
    {
        $section[0] = Utils\Str::replaceFirst('# ', '<span class="hide"># </span>', $section[0]);
        $this->richContentSections[$key] = $section;
    }
    /**
     * Set plain content section.
     *
     * @param string $key Content section key.
     * @param array $section Section content [title, content].
     */
    protected function setPlainContentSection(string $key, array $section) : void
    {
        $this->plainContentSections[$key] = $section;
    }

    /**
     * Set content section (CLI).
     *
     * @param string $key Console section key.
     * @param array $section Section content [title, <content>].
     */
    protected function setConsoleSection(string $key, array $section) : void
    {
        $section = array_map(function (string $value) {
            return strip_tags(html_entity_decode($value));
        }, $section);
        $this->consoleSections[$key] = $section;
    }
    /**
     * Sets content sections. This function is CLI aware.
     */
    protected function setContentSections() : self
    {
        // Plain (txt) is the default "always do" format.
        $plain = [
            static::SECTION_TITLE => ['%title% <span>in&nbsp;%file%:%line%</span>'],
            static::SECTION_MESSAGE => ['# Message', '%message%' . ($this->code ? ' [Code #%code%]' : null)],
            static::SECTION_TIME => ['# Time', '%datetimeUtc% [%timestamp%]'],
        ];
        $plain[static::SECTION_ID] = ['# Incident ID:%id%', 'Logged at %logFilename%'];
        $plain[self::SECTION_STACK] = ['Stack trace', '%plainStack%'];
        $plain[static::SECTION_CLIENT] = ['# Client', '%clientRemoteAddress% %clientUserAgent%'];
        $plain[static::SECTION_REQUEST] = ['# Request', '%serverProtocol% %requestMethod% %url%'];
        $plain[static::SECTION_SERVER] = ['# Server', '%serverName% (port:%serverPort%) %serverSoftware%'];

        if (php_sapi_name() == 'cli') {
            $verbosity = Console::output()->getVerbosity();
        }

        foreach ($plain as $k => $v) {
            $keyString = (string) $k;
            if (php_sapi_name() == 'cli' && static::CONSOLE_TABLE[$k] == false) {
                continue;
            }
            $this->setPlainContentSection($keyString, $v);
            if (isset($verbosity)) {
                $lvl = static::CONSOLE_TABLE[$k];
                if ($lvl === false || $verbosity < $lvl) {
                    continue;
                }
                if ($k == self::SECTION_STACK) {
                    $v[1] = '%consoleStack%';
                }
                $this->setConsoleSection($keyString, $v);
            } else {
                if ($k == self::SECTION_STACK) {
                    $v[1] = '%richStack%';
                }
                $this->setRichContentSection($keyString, $v);
            }
        }
        return $this;
    }
    /**
     * Sets content properties
     *
     * Basically sets title and message.
     */
    protected function setContentProperties() : self
    {
        $this->title = $this->thrown;
        $this->message = nl2br($this->message);
        return $this;
    }
    /**
     * Pass $GLOBALS to content sections
     */
    protected function appendContentGlobals() : self
    {
        foreach (['GET', 'POST', 'FILES', 'COOKIE', 'SESSION', 'SERVER'] as $v) {
            $k = '_'. $v;
            $v = isset($GLOBALS[$k]) ? $GLOBALS[$k] : null;
            if ($v) {
                $this->setRichContentSection($k, ['$' . $k, $this->wrapTextHr('<pre>' . Utils\Dump::out($v) . '</pre>')]);
                $this->setPlainContentSection($k, ['$' . $k, strip_tags($this->wrapTextHr(Utils\DumpPlain::out($v)))]);
            }
        }
        return $this;
    }
    /**
     * Generate the content template from content sections
     */
    protected function generateContentTemplate() : self
    {
        $sections_length = count($this->plainContentSections);
        $i = 0;
        foreach ($this->plainContentSections as $k => $plainSection) {
            $richSection = $this->richContentSections[$k] ?? null;
            $section_length = count($plainSection);
            if ($i == 0 || isset($plainSection[1])) {
                $this->richContentTemplate .= '<div class="t' . ($i == 0 ? ' t--scream' : null) . '">' . $richSection[0] . '</div>';
                $this->plainContentTemplate .= html_entity_decode($plainSection[0]);
                if ($i == 0) {
                    $this->richContentTemplate .= "\n" . '<div class="hide">' . str_repeat('=', static::COLUMNS) . '</div>';
                    $this->plainContentTemplate .= "\n" . str_repeat('=', static::COLUMNS);
                }
            }
            if ($i > 0) {
                $j = $section_length == 1 ? 0 : 1;
                for ($j; $j<$section_length; $j++) {
                    if ($section_length > 1) {
                        $this->richContentTemplate .= "\n";
                        $this->plainContentTemplate .= "\n";
                    }
                    $this->richContentTemplate .= '<div class="c">' . $richSection[$j] . '</div>';
                    $this->plainContentTemplate .= $plainSection[$j];
                }
            }
            if ($i+1 < $sections_length) {
                $this->richContentTemplate .= "\n" . '<br>' . "\n";
                $this->plainContentTemplate .= "\n\n";
            }
            $i++;
        }
        return $this;
    }
    /**
     * Set table value
     *
     * Table stores the template placeholders and its value.
     *
     * @param string $key Table key.
     * @param mixed $value Value.
     */
    protected function setTableValue(string $key, $value) : void
    {
        $this->table["%$key%"] = $value;
    }
    /**
     * Get table value
     *
     * Retrieve a value stored in the table.
     *
     * @param string $key Table key.
     */
    protected function getTableValue(string $key)
    {
        return $this->table["%$key%"] ?? null;
    }
    /**
     * Parse content template with properties
     */
    protected function parseContentTemplate() : self
    {
        $properties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $this->setTableValue($property->getName(), $property->getValue($this));
        }
        $this->content = strtr($this->richContentTemplate, $this->table);
        $this->plainContent = strtr($this->plainContentTemplate, $this->table);
        $this->setTableValue('content', $this->content);
        return $this;
    }
    /**
     * Set HTML output
     */
    protected function setHtmlOutput() : void
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
    /**
     * Set console output
     */
    protected function setConsoleOutput() : void
    {
        foreach ($this->consoleSections as $k => $v) {
            if ($k == 'title') {
                $tpl = $v[0];
            } else {
                Console::io()->section(strtr($v[0], $this->table));
                $tpl = $v[1];
            }
            $message = strtr($tpl, $this->table);
            if ($k == 'title') {
                Console::io()->error($message);
            } else {
                Console::io()->writeln($message);
            }
        }
        Console::io()->writeln('');
    }
    /**
     * Set output property
     */
    protected function setOutput() : self
    {
        $this->headers = [];
        if ($this->isXmlHttpRequest == true) {
            $this->setJsonOutput();
        } else {
            if (php_sapi_name() == 'cli') {
                $this->setConsoleOutput();
            } else {
                $this->setHtmlOutput();
            }
        }
        return $this;
    }
    /**
     * Prints output HTTP (HTML+JSON)
     */
    protected function out() : void
    {
        if ($this->isXmlHttpRequest) {
            $response = new Http\JsonResponse();
        } else {
            $response = new Http\Response();
        }
        $response->setContent($this->output);
        $response->setLastModified(new Utils\DateTime());
        $response->setStatusCode(500);
        foreach ($this->headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        $response->send();
    }
    /**
     * Writes error log (monolog)
     */
    protected function loggerExec() : self
    {
        $log = strip_tags($this->plainContent);
        $log .= "\n\n" . str_repeat('=', static::COLUMNS);
        $this->logger->log($this->loggerLevel, $log);
        return $this;
    }
    /**
     * Returns the error type.
     *
     * @param int $code PHP error code.
     * 
     * @return string Error type (string), null if the error code doesn't match
     * any error type.
     */
    protected static function getErrorByCode(int $code) : ?string
    {
        return static::ERROR_TABLE[$code];
    }
    /**
     * Returns the logger level.
     *
     * @param int $code PHP error code.
     * 
     * @return string Logger level (string), null if the error code doesn't match
     * any error type.
     */
    protected static function getLoggerLevel(int $code) : ?string
    {
        return static::PHP_LOG_LEVEL[$code] ?? null;
    }
    /**
     * Procedural-style error handler
     *
     * Turns every PHP error into an exception, for better error traceability.
     */
    public static function error($severity, $message, $file, $line) : void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    /**
     * Procedural-syle exception handler
     */
    public static function exception($e) : void
    {
        static::exceptionHandler(...func_get_args());
    }
}
