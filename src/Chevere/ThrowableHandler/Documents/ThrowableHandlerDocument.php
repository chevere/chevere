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

namespace Chevere\ThrowableHandler\Documents;

use Chevere\ThrowableHandler\Interfaces\ThrowableHandlerDocumentInterface;
use Chevere\ThrowableHandler\Interfaces\ThrowableHandlerFormatInterface;
use Chevere\ThrowableHandler\Interfaces\ThrowableHandlerInterface;
use Chevere\ThrowableHandler\Interfaces\ThrowableReadInterface;
use Chevere\ThrowableHandler\ThrowableRead;
use Chevere\Trace\TraceFormat;
use DateTimeInterface;

abstract class ThrowableHandlerDocument implements ThrowableHandlerDocumentInterface
{
    protected ThrowableHandlerInterface $handler;

    protected ThrowableHandlerFormatInterface $format;

    protected array $sections = self::SECTIONS;

    protected array $template;

    protected array $tags;

    protected int $verbosity = 0;

    final public function __construct(ThrowableHandlerInterface $throwableHandler)
    {
        $this->handler = $throwableHandler;
        $this->format = $this->getFormat();
        $this->template = $this->getTemplate();
    }

    abstract public function getFormat(): ThrowableHandlerFormatInterface;

    final public function withVerbosity(int $verbosity): static
    {
        $new = clone $this;
        $new->verbosity = $verbosity;

        return $new;
    }

    final public function verbosity(): int
    {
        return $this->verbosity;
    }

    final public function __toString(): string
    {
        if ($this->verbosity > 0) {
            $this->handleVerbositySections();
        }
        $throwableRead = $this->handler->throwableRead();
        $dateTimeUtc = $this->handler->dateTimeUtc();
        $this->tags = [
            static::TAG_TITLE => $throwableRead->className() . ' thrown',
            static::TAG_MESSAGE => $throwableRead->message(),
            static::TAG_CODE_WRAP => $this->getThrowableReadCode($throwableRead),
            static::TAG_FILE_LINE => $throwableRead->file() . ':' . $throwableRead->line(),
            static::TAG_ID => $this->handler->id(),
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
            implode($this->format->getLineBreak(), array_filter($template)),
            $this->tags
        ));
    }

    public function getTemplate(): array
    {
        return [
            static::SECTION_TITLE => $this->getSectionTitle(),
            static::SECTION_CHAIN => $this->getSectionChain(),
            static::SECTION_MESSAGE => $this->getSectionMessage(),
            static::SECTION_ID => $this->getSectionId(),
            static::SECTION_TIME => $this->getSectionTime(),
            static::SECTION_STACK => $this->getSectionStack(),
            static::SECTION_SERVER => $this->getSectionServer(),
        ];
    }

    public function getContent(string $content): string
    {
        return $content;
    }

    public function getSectionTitle(): string
    {
        return $this->format
            ->wrapTitle(static::TAG_TITLE . ' in ' . static::TAG_FILE_LINE);
    }

    public function getSectionMessage(): string
    {
        return $this->format
            ->wrapSectionTitle('# Message ' . static::TAG_CODE_WRAP) .
            "\n" . $this->getContent(static::TAG_MESSAGE);
    }

    public function getSectionChain(): string
    {
        if (!$this->handler->throwableRead()->hasPrevious()) {
            return '';
        }
        $throwable = $this->handler->throwableRead()->previous();
        $return = '';
        do {
            $throwableRead = new ThrowableRead($throwable);
            $return .= $this->format->wrapSectionTitle(
                '# └ ' . $throwableRead->className() . ' thrown ' .
                $this->getThrowableReadCode($throwableRead) .
                "\n"
            );
            $return .= $this->getContent(
                $throwable->getMessage() .
                ' in ' . $this->format->wrapLink($throwableRead->file() . ':' . $throwableRead->line())
            );
            if ($throwable->getPrevious() !== null) {
                $return .= $this->format->getLineBreak();
            }
        } while ($throwable = $throwable->getPrevious());

        return $return;
    }

    public function getSectionId(): string
    {
        return $this->format
            ->wrapSectionTitle('# Incident ID:' . static::TAG_ID);
    }

    public function getSectionTime(): string
    {
        return $this->format->wrapSectionTitle('# Time') . "\n" .
            $this->getContent(
                static::TAG_DATE_TIME_UTC_ATOM .
                ' [' . static::TAG_TIMESTAMP . ']'
            );
    }

    public function getSectionStack(): string
    {
        return $this->format->wrapSectionTitle('# Stack trace') . "\n" .
            $this->getContent(static::TAG_STACK);
    }

    public function getSectionServer(): string
    {
        return $this->format->wrapSectionTitle('# Server') . "\n" .
            $this->getContent(static::TAG_PHP_UNAME);
    }

    protected function prepare(string $document): string
    {
        return $document;
    }

    protected function getThrowableReadCode(ThrowableReadInterface $throwableRead): string
    {
        return $throwableRead->code() !== '0'
            ? '[Code #' . $throwableRead->code() . ']'
            : '';
    }

    protected function getStackTrace(): string
    {
        return (new TraceFormat(
            $this->handler->throwableRead()->trace(),
            $this->format
        ))->__toString();
    }

    protected function handleVerbositySections(): void
    {
        $sectionsVerbosity = static::SECTIONS_VERBOSITY;
        foreach ($this->sections as $sectionName) {
            $verbosityLevel = $sectionsVerbosity[$sectionName] ?? 0;
            if ($this->verbosity < $verbosityLevel) {
                $key = array_search($sectionName, $this->sections, true);
                unset($this->sections[$key]);
            }
        }
    }
}
