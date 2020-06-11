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

use Chevere\Components\ExceptionHandler\TraceFormatter;
use Chevere\Interfaces\ExceptionHandler\DocumentInterface;
use Chevere\Interfaces\ExceptionHandler\ExceptionHandlerInterface;
use Chevere\Interfaces\ExceptionHandler\FormatterInterface;
use DateTimeInterface;

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
        $exception = $this->exceptionHandler->exceptionRead();
        $dateTimeUtc = $this->exceptionHandler->dateTimeUtc();
        $this->tags = [
            static::TAG_TITLE => $exception->className() . ' thrown',
            static::TAG_MESSAGE => $exception->message(),
            static::TAG_CODE_WRAP => $this->getExceptionCode(),
            static::TAG_FILE_LINE => $exception->file() . ':' . $exception->line(),
            static::TAG_ID => $this->exceptionHandler->id(),
            static::TAG_DATE_TIME_UTC_ATOM => $dateTimeUtc->format(DateTimeInterface::ATOM),
            static::TAG_TIMESTAMP => $dateTimeUtc->getTimestamp(),
            static::TAG_STACK => $this->getStackTrace(),
            static::TAG_PHP_UNAME => php_uname(),
        ];
        $template = [];
        foreach ($this->sections as $sectionName) {
            $template[] = $this->template[$sectionName] ?? null;
        }

        return $this->prepare(strtr(
            implode($this->formatter->getLineBreak(), array_filter($template)),
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
        return $this->formatter->wrapSectionTitle('# Incident ID:' . static::TAG_ID);
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

    public function getSectionServer(): string
    {
        return $this->formatter->wrapSectionTitle('# Server') . "\n" . static::TAG_PHP_UNAME;
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
        return $this->exceptionHandler->exceptionRead()->code() > 0
            ? '[Code #' . $this->exceptionHandler->exceptionRead()->code() . ']'
            : '';
    }

    private function getStackTrace(): string
    {
        return (new TraceFormatter(
            $this->exceptionHandler->exceptionRead()->trace(),
            $this->formatter
        ))->toString();
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
