<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\ExceptionHandler\Documents;

use DateTimeInterface;
use Chevere\Components\ExceptionHandler\Interfaces\DocumentInterface;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use Chevere\Components\ExceptionHandler\TraceFormatter;

abstract class AbstractDocument implements DocumentInterface
{
    protected ExceptionHandlerInterface $exceptionHandler;

    protected FormatterInterface $formatter;

    protected array $sections = self::SECTIONS;

    /** @var array [$sectionName => $value, ] */
    protected array $template;

    /** @var array [$tag => $value, ] */
    private array $tags;

    private int $verbosity = 0;

    abstract public function getFormatter(): FormatterInterface;

    /**
     * Creates a new instance.
     */
    final public function __construct(ExceptionHandlerInterface $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->formatter = $this->getFormatter();
        $this->template = $this->getTemplate();
    }

    final public function withVerbosity(int $verbosity): DocumentInterface
    {
        $new = clone $this;
        $new->verbosity = $verbosity;

        return $new;
    }

    final public function verbosity(): int
    {
        return $this->verbosity;
    }

    final public function toString(): string
    {
        if ($this->verbosity > 0) {
            $this->handleVerbositySections();
        }
        $exeption = $this->exceptionHandler->exception();
        $dateTimeUtc = $this->exceptionHandler->dateTimeUtc();
        $this->tags = [
            static::TAG_TITLE => $exeption->className() . ' thrown',
            static::TAG_MESSAGE => $exeption->message(),
            static::TAG_CODE_WRAP => $this->getExceptionCode(),
            static::TAG_FILE_LINE => $exeption->file() . ':' . $exeption->line(),
            static::TAG_ID => $this->exceptionHandler->id(),
            static::TAG_DATE_TIME_UTC_ATOM => $dateTimeUtc->format(DateTimeInterface::ATOM),
            static::TAG_TIMESTAMP => $dateTimeUtc->getTimestamp(),
            static::TAG_LOG_DESTINATION => $this->exceptionHandler->logDestination(),
            static::TAG_STACK => $this->getStackTrace(),
            static::TAG_PHP_UNAME => php_uname(),
        ];
        $this->handleRequestTags();
        $templated = [];
        foreach ($this->sections as $sectionName) {
            $templated[] = $this->template[$sectionName] ?? null;
        }

        return $this->prepare(strtr(
            implode($this->formatter->getLineBreak(), array_filter($templated)),
            $this->tags
        ));
    }

    public function getTemplate(): array
    {
        return [
            static::SECTION_TITLE => $this->getSectionTitle(),
            static::SECTION_MESSAGE => $this->getSectionMessage(),
            static::SECTION_ID => $this->getSectionId(),
            static::SECTION_TIME => $this->getSectionTime(),
            static::SECTION_STACK => $this->getSectionStack(),
            static::SECTION_CLIENT => $this->getSectionClient(),
            static::SECTION_REQUEST => $this->getSectionRequest(),
            static::SECTION_SERVER => $this->getSectionServer(),
        ];
    }

    public function getSectionTitle(): string
    {
        return $this->formatter->wrapTitle(static::TAG_TITLE . ' in ' . static::TAG_FILE_LINE);
    }

    public function getSectionMessage(): string
    {
        return $this->formatter->wrapSectionTitle('# Message ' . static::TAG_CODE_WRAP) . "\n" . static::TAG_MESSAGE;
    }

    public function getSectionId(): string
    {
        return $this->formatter->wrapSectionTitle('# Incident ID:' . static::TAG_ID)
            . "\n" . 'Logged at ' . $this->formatter->wrapLink(static::TAG_LOG_DESTINATION);
    }

    public function getSectionTime(): string
    {
        return $this->formatter->wrapSectionTitle('# Time') . "\n" . static::TAG_DATE_TIME_UTC_ATOM
            . ' [' . static::TAG_TIMESTAMP . ']';
    }

    public function getSectionStack(): string
    {
        return $this->formatter->wrapSectionTitle('# Stack trace') . "\n" . static::TAG_STACK;
    }

    public function getSectionClient(): string
    {
        return $this->formatter->wrapSectionTitle('# Client') . "\n" . static::TAG_CLIENT_IP . ' '
            . static::TAG_CLIENT_USER_AGENT;
    }

    public function getSectionRequest(): string
    {
        return $this->formatter->wrapSectionTitle('# Request') . "\n" . static::TAG_SERVER_PROTOCOL . ' '
            . static::TAG_REQUEST_METHOD . ' ' . static::TAG_URI;
    }

    public function getSectionServer(): string
    {
        return $this->formatter->wrapSectionTitle('# Server') . "\n" . static::TAG_PHP_UNAME . ' '
            . static::TAG_SERVER_SOFTWARE;
    }

    /**
     * Prepare the document, useful to wrap headers, scripts, etc.
     *
     * @param string $document The document generated (so far)
     */
    protected function prepare(string $document): string
    {
        return $document;
    }

    private function getExceptionCode(): string
    {
        return $this->exceptionHandler->exception()->code() > 0
            ? '[Code #' . $this->exceptionHandler->exception()->code() . ']'
            : '';
    }

    private function getStackTrace(): string
    {
        return (new TraceFormatter(
            $this->exceptionHandler->exception()->trace(),
            $this->formatter
        ))->toString();
    }

    private function handleRequestTags(): void
    {
        if ($this->exceptionHandler->hasRequest()) {
            $request = $this->exceptionHandler->request();
            $this->tags = array_merge($this->tags, [
                static::TAG_CLIENT_IP => '*MISSING CLIENT IP*',
                static::TAG_CLIENT_USER_AGENT => $request->getHeaderLine('User-Agent'),
                static::TAG_SERVER_PROTOCOL => $request->protocolString(),
                static::TAG_REQUEST_METHOD => $request->getMethod(),
                static::TAG_URI => $request->getUri()->getPath(),
                static::TAG_SERVER_HOST => $request->getHeaderLine('Host'),
            ]);
        } else {
            $keyRequest = array_search(static::SECTION_REQUEST, $this->sections);
            $keyClient = array_search(static::SECTION_CLIENT, $this->sections);
            unset($this->sections[$keyRequest], $this->sections[$keyClient]);
        }
    }

    private function handleVerbositySections(): void
    {
        $sectionsVerbosity = static::SECTIONS_VERBOSITY;
        foreach ($this->sections as $sectionName) {
            $verbosityLevel = $sectionsVerbosity[$sectionName] ?? 0;
            if ($this->verbosity < $verbosityLevel) {
                $key = array_search($sectionName, $this->sections);
                unset($this->sections[$key]);
            }
        }
    }
}

//     private function processContentGlobals()
//     {
//         // $globals = $this->exceptionHandler->request()->globals()->globals();
//         $globals = $GLOBALS;
//         foreach (['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] as $global) {
//             $val = $globals[$global] ?? null;
//             if (!empty($val)) {
//                 $dumperVarDump = (new VarDump(new Dumpeable($val), new DumperFormatter()))->withProcess();
//                 $plainVarDump = (new VarDump(new Dumpeable($val), new PlainFormatter()))->withProcess();
//                 $wrapped = $dumperVarDump->toString();
//                 if (!BootstrapInstance::get()->isCli()) {
//                     $wrapped = '<pre>' . $wrapped . '</pre>';
//                 }
//                 $this->setRichContentSection($global, ['$' . $global, $this->wrapStringHr($wrapped)]);
//                 $this->setPlainContentSection($global, ['$' . $global, strip_tags($this->wrapStringHr($plainVarDump->toString()))]);
//             }
//         }
//     }
