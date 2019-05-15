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

use Chevereto\Chevere\App;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Utils\Dump;
use Chevereto\Chevere\Utils\DumpPlain;
use Chevereto\Chevere\Utils\DateTime;
use Chevereto\Chevere\Utils\Str;
use Chevereto\Chevere\Interfaces\ErrorHandlerInterface;
use ErrorException;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;

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
class ErrorHandler extends ErrorHandlerAbstract implements ErrorHandlerInterface
{
    /**
     * @param mixed $args Arguments passed to the error exception (severity, message, file, line; Exception)
     */
    public function __construct($args)
    {
        $this->setArguments($args);
    }

    protected static function exceptionHandler()
    {
        $that = new static(...func_get_args());
        $that->setXhrConditional();
        $that->setTimeProperties();
        $that->setId();
        $that->setConfigFilepath();
        $that->setHr();
        $that->setCss();
        $that->setServer();
        $that->setDebug();
        $that->setBodyClass();
        $that->setExceptionProperties();
        $that->setLogDateFormat();
        $that->setLogFilename();
        $that->setLogger();
        $that->setStack();
        $that->setContentSections();
        $that->appendContentGlobals();
        $that->generateContentTemplate();
        $that->setContentProperties();
        $that->parseContentTemplate();
        $that->loggerWrite();
        $that->setOutput();
        $that->out();
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
}
