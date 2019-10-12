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

namespace Chevere\ExceptionHandler\src;

use const Chevere\CLI;

use Throwable;
use ErrorException;
use Symfony\Component\Console\Output\OutputInterface;
use Chevere\Console\Console;
use Chevere\Data\Traits\DataMethodTrait;
use Chevere\ExceptionHandler\ExceptionHandler;
use Chevere\VarDump\Formatters\DumperFormatter;
use Chevere\VarDump\Formatters\PlainFormatter;
use Chevere\VarDump\VarDump;

use function ChevereFn\stringReplaceFirst;

/**
 * Formats the error exception in HTML (default), console and plain text.
 */
final class Formatter
{
    use DataMethodTrait;

    /** @var string Number of fixed columns for plaintext display */
    const COLUMNS = 120;

    /** ExceptionHandler sections */
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

    /** @var ExceptionHandler */
    private $exceptionHandler;

    /** @var string */
    private $lineBreak;

    /** @var array */
    private $plainContentSections;

    /** @var array */
    private $richContentSections;

    private $consoleSections;

    /** @var Wrap */
    private $wrap;

    /** @var Throwable */
    private $exception;

    public function __construct(ExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->wrap = $this->exceptionHandler->wrap();
        $this->exception = $this->wrap->exception();
        $this->data = $this->wrap->data();
        $this->setServerProperties();
        $this->data = $this->data
            ->withMergedArray([
                'body' => null,
                'content' => null,
                'thrown' => $this->wrap->data()->key('className') . ' thrown',
            ]);
        $this->processStack();
        $this->processContentSections();
        $this->processContentGlobals();
        $this->data = $this->data
            ->withMergedArray([
                'title' => $this->data->key('thrown'),
                'bodyClass' => !headers_sent() ? 'body--flex' : 'body--block',
            ] + $this->exceptionHandler->data()->toArray());
    }

    public function withLineBreak(string $lineBreak): Formatter
    {
        $new = clone $this;
        $new->lineBreak = $lineBreak;

        return $new;
    }

    public function withCss(string $css): Formatter
    {
        $new = clone $this;
        $new->data = $new->data
            ->withKey('css', $css);

        return $new;
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

    private function setServerProperties()
    {
        if (CLI) {
            // dd($this->exceptionHandler->request());
            $this->data = $this->data
                ->withMergedArray([
                    'clientIp' => $this->exceptionHandler->request()->getGlobals()->argv()[0],
                    'clientUserAgent' => Console::inputString(),
                ]);
        } else {
            $wea = [
                'uri' => $this->exceptionHandler->request()->getUri()->getPath() ?? 'unknown',
                'clientUserAgent' => $this->exceptionHandler->request()->getHeaderLine('User-Agent'),
                'requestMethod' => $this->exceptionHandler->request()->getMethod(),
                // 'serverHost' => $this->exceptionHandler->request()->getHost(),
                // 'serverPort' => (int) $this->exceptionHandler->request()->getPort(),
                'serverProtocol' => $this->exceptionHandler->request()->protocolString(),
                // 'serverSoftware' => $this->exceptionHandler->request()->getHeaderLine('SERVER_SOFTWARE'),
                // 'clientIp' => $this->exceptionHandler->request()->getClientIp(),
            ];
            $this->data = $this->data
                ->withMergedArray($wea);
        }
    }

    private function processStack()
    {
        $trace = $this->wrap->exception()->getTrace();
        if ($this->wrap->exception() instanceof ErrorException) {
            $this->data = $this->data
                ->withKey('thrown', $this->wrap->data()->key('type'));
            unset($trace[0]);
        }
        $stack = new Stack($trace);
        if (CLI) {
            $this->data = $this->data->withKey('consoleStack', $stack->getConsole());
        }
        $this->data = $this->data
            ->withKey('richStack', $stack->getRich())
            ->withKey('plainStack', $stack->getPlain());
    }

    private function processContentSections()
    {
        $sections = [
            static::SECTION_TITLE => ['%title% <span>in&nbsp;%file%:%line%</span>'],
            static::SECTION_MESSAGE => ['# Message', '%message%' . ($this->wrap->data()->key('code') ? ' [Code #%code%]' : null)],
            static::SECTION_TIME => ['# Time', '%dateTimeAtom% [%timestamp%]'],
            static::SECTION_ID => ['# Incident ID:%id%', 'Logged at %logFilename%'],
            static::SECTION_STACK => ['# Stack trace', '%plainStack%'],
            static::SECTION_CLIENT => ['# Client', '%clientIp% %clientUserAgent%'],
            static::SECTION_REQUEST => ['# Request', '%serverProtocol% %requestMethod% %uri%'],
            static::SECTION_SERVER => ['# Server', '%serverHost% (port:%serverPort%) %serverSoftware%'],
        ];

        if (CLI) {
            $verbosity = Console::output()->getVerbosity();
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
            return true;
        }
        $this->handleSetRichStackSection($key, $value);
        $this->setRichContentSection($key, $value);

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
        $dumperVarDump = new VarDump(new DumperFormatter());
        $plainVarDump = new VarDump(new PlainFormatter());
        foreach (['GET', 'POST', 'FILES', 'COOKIE', 'SESSION', 'SERVER'] as $v) {
            $k = '_' . $v;
            $v = isset($GLOBALS[$k]) ? $GLOBALS[$k] : null;
            if ($v) {
                $dumperVarDump = $dumperVarDump->withDump($v);
                $plainVarDump = $plainVarDump->withDump($v);
                $wrapped = $dumperVarDump->toString();
                if (!CLI) {
                    $wrapped = '<pre>' . $wrapped . '</pre>';
                }
                $this->setRichContentSection($k, ['$' . $k, $this->wrapStringHr($wrapped)]);
                $this->setPlainContentSection($k, ['$' . $k, strip_tags($this->wrapStringHr($plainVarDump->toString()))]);
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
        $section[0] = stringReplaceFirst('# ', '<span class="hide"># </span>', $section[0]);
        $this->richContentSections[$key] = $section;
    }

    /**
     * @param string $text text to wrap
     *
     * @return string wrapped text
     */
    private function wrapStringHr(string $text): string
    {
        return $this->lineBreak . "\n" . $text . "\n" . $this->lineBreak;
    }
}
