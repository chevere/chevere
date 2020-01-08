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

namespace Chevere\Components\ExceptionHandler\src;

use Chevere\Components\App\Instances\BootstrapInstance;
use ErrorException;
use Throwable;

use Symfony\Component\Console\Output\OutputInterface;

use Chevere\Components\Data\Traits\DataMethodTrait;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\VarDump\Formatters\DumperFormatter;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\VarDump;

use function console;
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

    private ExceptionHandler $exceptionHandler;

    private string $lineBreak;

    private array $plainContentSections;

    private array $richContentSections;

    private array $consoleSections;

    private Wrap $wrap;

    private Throwable $exception;

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
            ->withAddedKey('css', $css);

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
        $request = $this->exceptionHandler->request();
        if (BootstrapInstance::get()->console()) {
            $this->data = $this->data
                ->withMergedArray([
                    'clientIp' => $request->globals()->argv()[0],
                    'clientUserAgent' => console()->inputString(),
                ]);
        } else {
            $this->data = $this->data
                ->withMergedArray([
                    'uri' => $request->getUri()->getPath() ?? 'unknown',
                    'clientUserAgent' => $request->getHeaderLine('User-Agent'),
                    'serverHost' => $request->getHeaderLine('Host'),
                    'requestMethod' => $request->getMethod(),
                    'serverProtocol' => $request->protocolString(),
                    'serverSoftware' => $request->getGlobals()->server()['SERVER_SOFTWARE'],
                    'clientIp' => $request->getGlobals()->server()['REMOTE_ADDR'],
                ]);
        }
    }

    private function processStack()
    {
        $trace = $this->wrap->exception()->getTrace();
        if ($this->wrap->exception() instanceof ErrorException) {
            $this->data = $this->data
                ->withAddedKey('thrown', $this->wrap->data()->key('type'));
            unset($trace[0]);
        }
        $stack = new Stack($trace);
        if (BootstrapInstance::get()->cli()) {
            $this->data = $this->data->withAddedKey('consoleStack', $stack->getConsole());
        }
        $this->data = $this->data
            ->withAddedKey('richStack', $stack->getRich())
            ->withAddedKey('plainStack', $stack->getPlain());
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
            static::SECTION_SERVER => ['# Server', '%serverHost% %serverSoftware%'],
        ];

        if (BootstrapInstance::get()->console()) {
            $verbosity = console()->output()->getVerbosity();
        }
        $this->buildContentSections($sections, $verbosity ?? null);
    }

    private function buildContentSections(array $sections, ?int $verbosity)
    {
        foreach ($sections as $k => $v) {
            if (BootstrapInstance::get()->cli() && false == static::CONSOLE_TABLE[$k]) {
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
        $globals = $this->exceptionHandler->request()->getGlobals()->globals();
        foreach (['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] as $global) {
            $val = $globals[$global] ?? null;
            if (!empty($val)) {
                $dumperVarDump = $dumperVarDump->withDump($val);
                $plainVarDump = $plainVarDump->withDump($val);
                $wrapped = $dumperVarDump->toString();
                if (!BootstrapInstance::get()->cli()) {
                    $wrapped = '<pre>' . $wrapped . '</pre>';
                }
                $this->setRichContentSection($global, ['$' . $global, $this->wrapStringHr($wrapped)]);
                $this->setPlainContentSection($global, ['$' . $global, strip_tags($this->wrapStringHr($plainVarDump->toString()))]);
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
        $section = array_map(fn (string $value) => strip_tags(html_entity_decode($value)), $section);
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
