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

use Throwable;
use ErrorException;
use const Chevere\CLI;
use Chevere\Console;
use Chevere\VarDumper\VarDumper;
use Chevere\VarDumper\PlainVarDumper;
use Chevere\Utility\Str;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Formats the error exception in HTML (default), console and plain text.
 */
class Formatter
{
    /** @var string Number of fixed columns for plaintext display */
    const COLUMNS = 120;

    /** ErrorHandler sections */
    const SECTION_TITLE = 'title';
    const SECTION_MESSAGE = 'message';
    const SECTION_ID = 'id';
    const SECTION_TIME = 'time';
    const SECTION_STACK = 'stack';
    const SECTION_CLIENT = 'client';
    const SECTION_REQUEST = 'request';
    const SECTION_SERVER = 'server';

    /** Verbose aware console sections. */
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

    /** @var ErrorHandler */
    public $errorHandler;

    /** @var string */
    public $css;

    /** @var string */
    public $lineBreak;

    /** @var string IP address */
    public $clientIp;

    /** @var string User-Agent */
    public $clientUserAgent;

    /** @var string The request URI */
    public $url;

    /** @var string */
    public $requestMethod;

    /** @var string */
    public $serverHost;

    /** @var int */
    public $serverPort;

    /** @var string */
    public $serverProtocol;

    /** @var string */
    public $serverSoftware;

    /** @var string */
    public $bodyClass;

    /** @var Throwable */
    protected $exception;

    /** @var string */
    public $className;

    /** @var string FIXME: Better name */
    public $thrown;

    // The actual stacks
    public $plainStack;
    public $richStack;
    public $consoleStack;

    /** @var array */
    public $plainContentSections;

    /** @var array */
    public $richContentSections;

    public $consoleSections;

    // Exception properties FIXME: BETTER NAMES
    public $code;
    public $message;
    public $type;
    public $file;
    public $line;

    /** @var string */
    public $loggerLevel;

    public $title;

    public $table;

    /** @var ExceptionHandler */
    protected $exceptionHandler;

    /** @var string */
    public $uri;

    public function __construct(ErrorHandler $errorHandler, ExceptionHandler $exceptionHandler)
    {
        $this->errorHandler = $errorHandler;
        $this->exceptionHandler = $exceptionHandler;
        $this->processServerProperties();
        $this->proccessExceptionHandler();
        $this->processStack();
        $this->processContentSections();
        $this->processContentGlobals();
        $this->setContentProperties();
        $this->setBodyClass();
    }

    public function setLineBreak(string $lineBreak)
    {
        $this->lineBreak = $lineBreak;
    }

    public function setCss(string $css)
    {
        $this->css = $css;
    }

    protected function processServerProperties()
    {
        if (CLI) {
            $this->clientIp = $_SERVER['argv'][0];
            $this->clientUserAgent = Console::inputString();
        } else {
            $request = $this->errorHandler->request;
            if (isset($request)) {
                $this->uri = $request->readInfoKey('requestUri') ?? 'unknown';
                $this->clientUserAgent = $request->getHeaders()->get('User-Agent');
                $this->httpRequestMethod = $request->readInfoKey('method');
                $this->serverHost = $request->readInfoKey('host');
                $this->serverPort = (int) $request->readInfoKey('port');
                $this->serverProtocol = $request->readInfoKey('protocolVersion');
                $this->serverSoftware = $request->getServer()->get('SERVER_SOFTWARE');
                $this->clientIp = $request->readInfoKey('clientIp');
            }
        }
    }

    protected function setBodyClass()
    {
        $this->bodyClass = !headers_sent() ? 'body--flex' : 'body--block';
    }

    protected function proccessExceptionHandler()
    {
        $this->exception = $this->exceptionHandler->exception;
        $this->className = $this->exceptionHandler->className;
        $this->thrown = $this->className . ' thrown';
        $this->code = $this->exceptionHandler->code;
        $this->type = $this->exceptionHandler->type;
        $this->loggerLevel = $this->exceptionHandler->loggerLevel;
        $this->message = $this->exceptionHandler->message;
        $this->file = $this->exceptionHandler->file;
        $this->line = $this->exceptionHandler->line;
    }

    protected function processStack()
    {
        $trace = $this->exceptionHandler->exception->getTrace();
        if ($this->exceptionHandler->exception instanceof ErrorException) {
            $this->thrown = $this->exceptionHandler->type;
            unset($trace[0]);
        }
        $stack = new Stack($trace);
        if (CLI) {
            $this->consoleStack = $stack->getConsoleStack();
        }
        $this->richStack = $stack->getRichStack();
        $this->plainStack = $stack->getPlainStack();
    }

    protected function processContentSections()
    {
        $sections = [
            static::SECTION_TITLE => ['%title% <span>in&nbsp;%file%:%line%</span>'],
            static::SECTION_MESSAGE => ['# Message', '%message%' . ($this->exceptionHandler->code ? ' [Code #%code%]' : null)],
            static::SECTION_TIME => ['# Time', '%datetimeUtc% [%timestamp%]'],
            static::SECTION_ID => ['# Incident ID:%id%', 'Logged at %logFilename%'],
            static::SECTION_STACK => ['# Stack trace', '%plainStack%'],
            static::SECTION_CLIENT => ['# Client', '%clientIp% %clientUserAgent%'],
            static::SECTION_REQUEST => ['# Request', '%serverProtocol% %httpRequestMethod% %uri%'],
            static::SECTION_SERVER => ['# Server', '%serverHost% (port:%serverPort%) %serverSoftware%'],
        ];

        if (CLI) {
            $verbosity = Console::output()->getVerbosity();
        }
        $this->buildContentSections($sections, $verbosity ?? null);
    }

    protected function buildContentSections(array $sections, ?int $verbosity)
    {
        foreach ($sections as $k => $v) {
            if (CLI && false == static::CONSOLE_TABLE[$k]) {
                continue;
            }
            if (false === $this->processContentSectionsArray((string) $k, $v, $verbosity)) {
                continue;
            }
        }
    }

    protected function processContentSectionsArray(string $key, array $value, ?int $verbosity): bool
    {
        $this->setPlainContentSection($key, $value);
        if (isset($verbosity)) {
            $verbosityLevel = static::CONSOLE_TABLE[$key];
            if (false === $verbosityLevel || $verbosity < $verbosityLevel) {
                return false;
            }
            $this->handleSetConsoleStackSection($key, $value);
            $this->setConsoleSection($key, $value);
        } else {
            $this->handleSetRichStackSection($key, $value);
            $this->setRichContentSection($key, $value);
        }

        return true;
    }

    protected function handleSetRichStackSection(string $key, array &$value)
    {
        if ($key == static::SECTION_STACK) {
            $value[1] = '%richStack%';
        }
    }

    protected function handleSetConsoleStackSection(string $key, array &$value)
    {
        if ($key == static::SECTION_STACK) {
            $value[1] = '%consoleStack%';
        }
    }

    protected function processContentGlobals()
    {
        foreach (['GET', 'POST', 'FILES', 'COOKIE', 'SESSION', 'SERVER'] as $v) {
            $k = '_' . $v;
            $v = isset($GLOBALS[$k]) ? $GLOBALS[$k] : null;
            if ($v) {
                $this->setRichContentSection($k, ['$' . $k, $this->wrapStringHr('<pre>' . VarDumper::out($v) . '</pre>')]);
                $this->setPlainContentSection($k, ['$' . $k, strip_tags($this->wrapStringHr(PlainVarDumper::out($v)))]);
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

    protected function setContentProperties()
    {
        $this->title = $this->thrown;
        $this->message = nl2br($this->message);
    }

    /**
     * @param string $text text to wrap
     *
     * @return string wrapped text
     */
    protected function wrapStringHr(string $text): string
    {
        return $this->lineBreak . "\n" . $text . "\n" . $this->lineBreak;
    }
}
