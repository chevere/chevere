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

use Chevere\Components\ExceptionHandler\Interfaces\DocumentInterface;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use Chevere\Components\ExceptionHandler\Trace;
use DateTimeInterface;

abstract class AbstractDocument implements DocumentInterface
{
    protected ExceptionHandlerInterface $exceptionHandler;

    protected FormatterInterface $formatter;

    protected array $sections = self::SECTIONS;

    protected array $sectionsTemplate;

    private array $tags;

    final public function __construct(ExceptionHandlerInterface $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->formatter = $this->getFormatter();
        $this->sectionsTemplate = $this->getSectionsTemplate();
    }

    /**
     * @return string[]
     */
    final public function sections(): array
    {
        return $this->sections;
    }

    protected function prepare(string $value): string
    {
        return $value;
    }

    protected function getGlue(): string
    {
        return "\n\n";
    }

    abstract public function getSectionsTemplate(): array;

    abstract public function getFormatter(): FormatterInterface;

    final public function toString(): string
    {
        $exeption = $this->exceptionHandler->exception();
        $dateTimeUtc = $this->exceptionHandler->dateTimeUtc();
        $this->tags = [
            static::TAG_TITLE => $exeption->className() . ' thrown',
            static::TAG_MESSAGE => $exeption->message(),
            static::TAG_CODE_WRAP => $this->getCodeWrap(),
            static::TAG_FILE_LINE => $exeption->file() . ':' . $exeption->line(),
            static::TAG_ID => $this->exceptionHandler->id(),
            static::TAG_DATE_TIME_UTC_ATOM => $dateTimeUtc->format(DateTimeInterface::ATOM),
            static::TAG_TIMESTAMP => $dateTimeUtc->getTimestamp(),
            static::TAG_LOG_FILENAME => $this->getLogFilename(),
            static::TAG_STACK => $this->getStack(),
            static::TAG_PHP_UNAME => php_uname(),
        ];
        $this->handleRequestTags();
        $templated = [];
        foreach ($this->sections as $sectionName) {
            $templated[] = $this->sectionsTemplate[$sectionName] ?? null;
        }
        $templated = array_filter($templated);
        $preDocument = implode($this->getGlue(), $templated);
        $document = strtr($preDocument, $this->tags);

        return $this->prepare($document);
    }

    private function getCodeWrap(): string
    {
        return
            $this->exceptionHandler->exception()->code() > 0
            ? '[Code #' . $this->exceptionHandler->exception()->code() . ']'
            : '';
    }

    private function getLogFilename(): string
    {
        return $this->exceptionHandler->hasLogger() ? '__LOGGER_FILENAME__' : '/dev/null';
    }

    private function getStack(): string
    {
        return
            (new Trace($this->exceptionHandler->exception()->trace(), $this->formatter))
                ->toString();
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
}
