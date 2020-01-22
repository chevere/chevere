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

    protected array $sections;

    protected array $sectionsTemplate;

    final public function __construct(ExceptionHandlerInterface $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->formatter = $this->getFormatter();
        $this->sections = $this->getSections();
        $this->sectionsTemplate = $this->getSectionsTemplate();
    }

    final public function toString(): string
    {
        $exeption = $this->exceptionHandler->exception();
        $dateTimeUtc = $this->exceptionHandler->dateTimeUtc();
        $tags = [
            static::TAG_TITLE => $exeption->className() . ' thrown',
            static::TAG_MESSAGE => $exeption->message(),
            static::TAG_CODE_WRAP => $exeption->code() > 0 ? '[Code #' . $exeption->code() . ']' : '',
            static::TAG_FILE_LINE => $exeption->file() . ':' . $exeption->line(),
            static::TAG_ID => $this->exceptionHandler->id(),
            static::TAG_DATE_TIME_UTC_ATOM => $dateTimeUtc->format(DateTimeInterface::ATOM),
            static::TAG_TIMESTAMP => $dateTimeUtc->getTimestamp(),
            static::TAG_LOG_FILENAME => $this->exceptionHandler->hasLogger() ? '__LOGGER_FILENAME__' : '/dev/null',
            static::TAG_STACK => (new Trace($exeption->trace(), $this->formatter))->toString(),
            static::TAG_PHP_UNAME => php_uname(),
        ];
        if ($this->exceptionHandler->hasRequest()) {
            $request = $this->exceptionHandler->request();
            $tags = array_merge($tags, [
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
            $tags = array_merge($tags, [
                static::TAG_CLIENT_IP => '',
                static::TAG_CLIENT_USER_AGENT => '',
                static::TAG_SERVER_PROTOCOL => '',
                static::TAG_REQUEST_METHOD => '',
                static::TAG_URI => '',
                static::TAG_SERVER_HOST => '',
            ]);
        }
        $templated = [];
        foreach ($this->sections as $pos => $sectionName) {
            $templated[] = $this->sectionsTemplate[$sectionName];
        }
        $preDocument = implode("\n\n", $templated);
        $document = strtr($preDocument, $tags);

        return $document;
    }

    /**
     * @return string[]
     */
    final public function sections(): array
    {
        return $this->sections;
    }

    abstract public function getFormatter(): FormatterInterface;

    /**
     * @return string[]
     */
    public function getSections(): array
    {
        return [
            0 => static::SECTION_TITLE,
            1 => static::SECTION_MESSAGE,
            2 => static::SECTION_ID,
            3 => static::SECTION_TIME,
            4 => static::SECTION_STACK,
            5 => static::SECTION_CLIENT,
            6 => static::SECTION_REQUEST,
            7 => static::SECTION_SERVER,
        ];
    }

    public function getSectionsTemplate(): array
    {
        return [
            static::SECTION_TITLE => '%title% in %fileLine%',
            static::SECTION_MESSAGE => '# Message' . "\n" . '%message% %codeWrap%',
            static::SECTION_TIME => '# Time' . "\n" . '%dateTimeUtcAtom% [%timestamp%]',
            static::SECTION_ID => '# Incident ID:%id%' . "\n" . 'Logged at %logFilename%',
            static::SECTION_STACK => '# Stack trace' . "\n" . '%stack%',
            static::SECTION_CLIENT => '# Client' . "\n" . '%clientIp% %clientUserAgent%',
            static::SECTION_REQUEST => '# Request' . "\n" . '%serverProtocol% %requestMethod% %uri%',
            static::SECTION_SERVER => '# Server' . "\n" . '%serverHost% %serverSoftware%',
        ];
    }
}
