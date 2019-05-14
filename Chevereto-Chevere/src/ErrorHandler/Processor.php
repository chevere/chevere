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

use const Chevereto\Chevere\CORE_NS_HANDLE;
use const Chevereto\Chevere\App\PATH;
use Chevereto\Chevere\App;
use Chevereto\Chevere\Json;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Path;
use Chevereto\Chevere\Utils\Dump;
use Chevereto\Chevere\Utils\DumpPlain;
use Chevereto\Chevere\Utils\DateTime;
use Chevereto\Chevere\Utils\Str;
use Throwable;
use ErrorException;
use ReflectionObject;
use ReflectionProperty;
use DateTimeZone;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;

abstract class Processor
{
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
     * Detects XMLHttpRequest (XHR).
     */
    protected function setXhrConditional()
    {
        // if (App::instance() && App::instance()->getRequest() instanceof App) {
        //     $this->isXmlHttpRequest = App::instance()->getRequest()->isXmlHttpRequest();
        // } else {
        //     $this->isXmlHttpRequest = Http::isXHR();
        //
    }

    /**
     * Sets signature properties (time + id).
     *
     * Assign properties used to identify the error incident.
     */
    protected function setSignatureProperties()
    {
        $this->datetimeUtc = DateTime::getUTC();
        $this->timestamp = @strtotime($this->datetimeUtc);
        $this->id = uniqid();
    }

    /**
     * Sets constant properties from const values.
     */
    protected function setConstantProperties()
    {
        // Set these directly from const
        $this->configFilePath = static::CONFIG_FILE_PATH;
        $this->css = static::CSS;
        $this->hr = static::HR;
    }

    protected function setLogProperties()
    {
        $this->setLogDateFormat();
        $this->setLogFilename();
    }

    protected function setLogDateFormat()
    {
        $this->logDateFormat = static::LOG_DATE_FOLDER;
    }

    /**
     * Sets log filename.
     *
     * Both App and Chevereto\Chevere logs to app/var/logs.
     */
    protected function setLogFilename(string $path = null)
    {
        if (!isset($path)) {
            $path = static::PATH_LOGS;
        }
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

    /**
     * Set debug flag from const DEBUG > Config.
     *
     * If debug is enabled, all the error information will be printed.
     * If disabled, it will hide the error content.
     * In any case, this class should always logs everything into the error log.
     */
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

    /**
     * Sets body class based on headers sent.
     *
     * If headers have been sent, it will use display:block instead of
     * display:flex.
     */
    protected function setBodyClass()
    {
        $this->bodyClass = !headers_sent() ? 'body--flex' : 'body--block';
    }

    /**
     * Wraps text with hr.
     *
     * @param string $text text to wrap
     *
     * @return string wrapped text
     */
    protected function wrapTextHr(string $text): string
    {
        return $this->hr . "\n" . $text . "\n" . $this->hr;
    }

    /**
     * Set arguments.
     *
     * When called, the exception arguments are passed directly to this method.
     *
     * @see exceptionHandler
     */
    protected function setArguments()
    {
        $this->arguments = func_get_args();
    }

    /**
     * Sets the exeption properties from the Exception object.
     */
    protected function setExceptionProperties()
    {
        $this->exception = $this->arguments[0];
        $this->className = get_class($this->exception);
        if (Str::startsWith(CORE_NS_HANDLE, $this->className)) {
            // Get rid of own namespace
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

    /**
     * Sets the server properties from $_SERVER.
     */
    protected function setServerProperties()
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

    /**
     * Generate JSON response.
     */
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

    /**
     * Set stacks from exception trace. This class is CLI aware.
     */
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
        $errorHandlerStack = new Stack($trace);
        $glue = "\n" . $this->hr . "\n";
        $this->consoleStack = strip_tags(implode($glue, $errorHandlerStack->getConsole()));
        $this->richStack = $this->wrapTextHr(implode($glue, $errorHandlerStack->getRich()));
        $this->plainStack = $this->wrapTextHr(implode($glue, $errorHandlerStack->getPlain()));
    }

    /**
     * Set rich content section.
     *
     * @param string $key     content section key
     * @param array  $section section content [title, content]
     */
    protected function setRichContentSection(string $key, array $section): void
    {
        $section[0] = Str::replaceFirst('# ', '<span class="hide"># </span>', $section[0]);
        $this->richContentSections[$key] = $section;
    }

    /**
     * Set plain content section.
     *
     * @param string $key     content section key
     * @param array  $section section content [title, content]
     */
    protected function setPlainContentSection(string $key, array $section): void
    {
        $this->plainContentSections[$key] = $section;
    }

    /**
     * Set content section (CLI).
     *
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
     * Sets content sections. This function is CLI aware.
     */
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
     * Sets content properties.
     *
     * Basically sets title and message.
     */
    protected function setContentProperties()
    {
        $this->title = $this->thrown;
        $this->message = nl2br($this->message);
    }

    /**
     * Pass $GLOBALS to content sections.
     */
    protected function appendContentGlobals()
    {
        foreach (['GET', 'POST', 'FILES', 'COOKIE', 'SESSION', 'SERVER'] as $v) {
            $k = '_' . $v;
            $v = isset($GLOBALS[$k]) ? $GLOBALS[$k] : null;
            if ($v) {
                $this->setRichContentSection($k, ['$' . $k, $this->wrapTextHr('<pre>' . Dump::out($v) . '</pre>')]);
                $this->setPlainContentSection($k, ['$' . $k, strip_tags($this->wrapTextHr(DumpPlain::out($v)))]);
            }
        }
    }

    /**
     * Generate the content template from content sections.
     */
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

    /**
     * Set table value.
     *
     * Table stores the template placeholders and its value.
     *
     * @param string $key   table key
     * @param mixed  $value value
     */
    protected function setTableValue(string $key, $value): void
    {
        $this->table["%$key%"] = $value;
    }

    /**
     * Get table value.
     *
     * Retrieve a value stored in the table.
     *
     * @param string $key table key
     */
    protected function getTableValue(string $key)
    {
        return $this->table["%$key%"] ?? null;
    }

    /**
     * Parse content template with properties.
     */
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

    /**
     * Set HTML output.
     */
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

    /**
     * Set console output.
     */
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

    /**
     * Set output property.
     */
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

    /**
     * Prints output HTTP (HTML+JSON).
     */
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

    /**
     * Writes error log (monolog).
     */
    protected function loggerExec()
    {
        $log = strip_tags($this->plainContent);
        $log .= "\n\n" . str_repeat('=', static::COLUMNS);
        $this->logger->log($this->loggerLevel, $log);
    }

    /**
     * Returns the error type.
     *
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
     * Returns the logger level.
     *
     * @param int $code PHP error code
     *
     * @return string logger level (string), null if the error code doesn't match
     *                any error type
     */
    protected static function getLoggerLevel(int $code): ?string
    {
        return static::PHP_LOG_LEVEL[$code] ?? null;
    }
}
