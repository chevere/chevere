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

namespace Chevere\ErrorHandler\src;

use Throwable;
use ErrorException;
use Symfony\Component\Console\Output\OutputInterface;
use const Chevere\CLI;
use Chevere\Console\Console;
use Chevere\ErrorHandler\ErrorHandler;
use Chevere\ErrorHandler\ExceptionHandler;
use Chevere\VarDump\VarDump;
use Chevere\VarDump\PlainVarDump;
use Chevere\Utility\Str;
use Chevere\Data\Data;

/**
 * Formats the error exception in HTML (default), console and plain text.
 */
final class Formatter
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
    private $errorHandler;

    /** @var string */
    private $lineBreak;

    /** @var array */
    private $plainContentSections;

    /** @var array */
    private $richContentSections;

    private $consoleSections;

    /** @var string */
    private $varDump;

    /** @var ExceptionHandler */
    private $exceptionHandler;

    /** @var Throwable */
    private $exception;

    /** @var Data */
    private $data;

    public function __construct(ErrorHandler $errorHandler, ExceptionHandler $exceptionHandler)
    {
        $this->varDump = VarDump::RUNTIME;
        $this->errorHandler = $errorHandler;
        $this->exceptionHandler = $exceptionHandler;
        $this->exception = $this->exceptionHandler->exception();
        $this->data = $this->exceptionHandler->data();
        $this->setServerProperties();
        $this->data->add([
            'thrown' => $this->exceptionHandler->dataKey('className').' thrown',
        ]);
        $this->processStack();
        $this->processContentSections();
        $this->processContentGlobals();
        $this->data->add([
            'title' => $this->data->getKey('thrown'),
            'bodyClass' => !headers_sent() ? 'body--flex' : 'body--block',
        ]);
    }

    public function setLineBreak(string $lineBreak)
    {
        $this->lineBreak = $lineBreak;
    }

    public function setCss(string $css)
    {
        $this->data->setKey('css', $css);
    }

    public function plainContentSections(): array
    {
        return $this->plainContentSections;
    }

    public function richContentSections(): array
    {
        return $this->richContentSections;
    }

    public function consoleSections(): array
    {
        return $this->consoleSections;
    }

    public function getTemplateTags(): array
    {
        return [
            '%id%' => $this->errorHandler->dataKey('id'),
            '%datetimeUtc%' => $this->errorHandler->dataKey('dateTimeAtom'),
            '%timestamp%' => $this->errorHandler->dataKey('timestamp'),
            '%loadedConfigFilesString%' => $this->errorHandler->dataKey('loadedConfigFilesString'),
            '%logFilename%' => $this->errorHandler->dataKey('logFilename'),
            '%css%' => $this->data->getKey('css'),
            '%bodyClass%' => $this->data->getKey('bodyClass'),
            '%body%' => null,
            '%title%' => $this->data->getKey('title'),
            '%content%' => null,
            '%title%' => $this->data->getKey('title'),
            '%file%' => $this->data->getKey('file'),
            '%line%' => $this->data->getKey('line'),
            '%message%' => $this->data->getKey('message'),
            '%code%' => $this->data->getKey('code'),
            '%plainStack%' => $this->data->getKey('plainStack'),
            '%consoleStack%' => $this->data->getKey('consoleStack'),
            '%richStack%' => $this->data->getKey('richStack'),
            '%clientIp%' => $this->data->getKey('clientIp'),
            '%clientUserAgent%' => $this->data->getKey('clientUserAgent'),
            '%serverProtocol%' => $this->data->getKey('serverProtocol'),
            '%requestMethod%' => $this->data->getKey('requestMethod'),
            '%uri%' => $this->data->getKey('uri') ?? null,
            '%serverHost%' => $this->data->getKey('serverHost'),
            '%serverPort%' => $this->data->getKey('serverPort'),
            '%serverSoftware%' => $this->data->getKey('serverSoftware'),
        ];
    }

    private function setServerProperties()
    {
        if (CLI) {
            $this->data->add([
                'clientIp' => $_SERVER['argv'][0],
                'clientUserAgent' => Console::inputString(),
            ]);
        } else {
            $this->data->add([
                // FIXME: Drop this
                'uri' => $this->errorHandler->request()->readInfoKey('requestUri') ?? 'unknown',
                'clientUserAgent' => $this->errorHandler->request()->headers->get('User-Agent'),
                'requestMethod' => $this->errorHandler->request()->readInfoKey('method'),
                'serverHost' => $this->errorHandler->request()->readInfoKey('host'),
                'serverPort' => (int) $this->errorHandler->request()->readInfoKey('port'),
                'serverProtocol' => $this->errorHandler->request()->readInfoKey('protocolVersion'),
                'serverSoftware' => $this->errorHandler->request()->headers->get('SERVER_SOFTWARE'),
                'clientIp' => $this->errorHandler->request()->readInfoKey('clientIp'),
            ]);
        }
    }

    private function processStack()
    {
        $trace = $this->exceptionHandler->exception()->getTrace();
        if ($this->exceptionHandler->exception() instanceof ErrorException) {
            $this->data->setKey('thrown', $this->exceptionHandler->type());
            unset($trace[0]);
        }
        $stack = new Stack($trace);
        if (CLI) {
            $this->data->setKey('consoleStack', $stack->getConsole());
        }
        $this->data->setKey('richStack', $stack->getRich());
        $this->data->setKey('plainStack', $stack->getPlain());
    }

    private function processContentSections()
    {
        $sections = [
            static::SECTION_TITLE => ['%title% <span>in&nbsp;%file%:%line%</span>'],
            static::SECTION_MESSAGE => ['# Message', '%message%'.($this->exceptionHandler->dataKey('code') ? ' [Code #%code%]' : null)],
            static::SECTION_TIME => ['# Time', '%datetimeUtc% [%timestamp%]'],
            static::SECTION_ID => ['# Incident ID:%id%', 'Logged at %logFilename%'],
            static::SECTION_STACK => ['# Stack trace', '%plainStack%'],
            static::SECTION_CLIENT => ['# Client', '%clientIp% %clientUserAgent%'],
            static::SECTION_REQUEST => ['# Request', '%serverProtocol% %requestMethod% %uri%'],
            static::SECTION_SERVER => ['# Server', '%serverHost% (port:%serverPort%) %serverSoftware%'],
        ];

        if (CLI) {
            $verbosity = Console::cli()->output->getVerbosity();
        }
        $this->buildContentSections($sections, $verbosity ?? null);
    }

    private function buildContentSections(array $sections, ?int $verbosity)
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

    private function processContentSectionsArray(string $key, array $value, ?int $verbosity): bool
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

    private function handleSetRichStackSection(string $key, array &$value)
    {
        if ($key == static::SECTION_STACK) {
            $value[1] = '%richStack%';
        }
    }

    private function handleSetConsoleStackSection(string $key, array &$value)
    {
        if ($key == static::SECTION_STACK) {
            $value[1] = '%consoleStack%';
        }
    }

    private function processContentGlobals()
    {
        foreach (['GET', 'POST', 'FILES', 'COOKIE', 'SESSION', 'SERVER'] as $v) {
            $k = '_'.$v;
            $v = isset($GLOBALS[$k]) ? $GLOBALS[$k] : null;
            if ($v) {
                $wrapped = $this->varDump::out($v);
                if (!CLI) {
                    $wrapped = '<pre>'.$wrapped.'</pre>';
                }
                $this->setRichContentSection($k, ['$'.$k, $this->wrapStringHr($wrapped)]);
                $this->setPlainContentSection($k, ['$'.$k, strip_tags($this->wrapStringHr(PlainVarDump::out($v)))]);
            }
        }
    }

    /**
     * @param string $key     content section key
     * @param array  $section section content [title, content]
     */
    private function setPlainContentSection(string $key, array $section): void
    {
        $this->plainContentSections[$key] = $section;
    }

    /**
     * @param string $key     console section key
     * @param array  $section section content [title, <content>]
     */
    private function setConsoleSection(string $key, array $section): void
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
    private function setRichContentSection(string $key, array $section): void
    {
        $section[0] = Str::replaceFirst('# ', '<span class="hide"># </span>', $section[0]);
        $this->richContentSections[$key] = $section;
    }

    /**
     * @param string $text text to wrap
     *
     * @return string wrapped text
     */
    private function wrapStringHr(string $text): string
    {
        return $this->lineBreak."\n".$text."\n".$this->lineBreak;
    }
}
