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

namespace Chevere\Interfaces\ThrowableHandler;

use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of defining a throwable handler document.
 */
interface ThrowableHandlerDocumentInterface extends ToStringInterface
{
    const SECTION_TITLE = 'title';
    const SECTION_MESSAGE = 'message';
    const SECTION_ID = 'id';
    const SECTION_TIME = 'time';
    const SECTION_STACK = 'stack';
    const SECTION_SERVER = 'server';

    const TAG_TITLE = '%title%';
    const TAG_MESSAGE = '%message%';
    const TAG_CODE_WRAP = '%codeWrap%';
    const TAG_ID = '%id%';
    const TAG_FILE_LINE = '%fileLine%';
    const TAG_DATE_TIME_UTC_ATOM = '%dateTimeUtcAtom%';
    const TAG_TIMESTAMP = '%timestamp%';
    const TAG_STACK = '%stack%';
    const TAG_PHP_UNAME = '%phpUname%';

    const SECTIONS = [
        self::SECTION_TITLE,
        self::SECTION_MESSAGE,
        self::SECTION_ID,
        self::SECTION_TIME,
        self::SECTION_STACK,
        self::SECTION_SERVER,
    ];

    const SECTIONS_VERBOSITY = [
        self::SECTION_TITLE => 16,
        self::SECTION_MESSAGE => 16,
        self::SECTION_ID => 16,
        self::SECTION_TIME => 64,
        self::SECTION_STACK => 128,
        self::SECTION_SERVER => 64,
    ];

    public function __construct(ThrowableHandlerInterface $throwableHandler);

    /**
     * Return an instance with the specified verbosity.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified verbosity.
     *
     * Calling this method will reset the document sections to fit the target verbosity.
     */
    public function withVerbosity(int $verbosity): ThrowableHandlerDocumentInterface;

    /**
     * Provides access to the instance verbosity.
     */
    public function verbosity(): int;

    /**
     * Returns the document title section.
     */
    public function getSectionTitle(): string;

    /**
     * Returns the document message section.
     */
    public function getSectionMessage(): string;

    /**
     * Returns the document id section.
     */
    public function getSectionId(): string;

    /**
     * Returns the document time section.
     */
    public function getSectionTime(): string;

    /**
     * Returns the document stack section.
     */
    public function getSectionStack(): string;

    /**
     * Returns the document server section.
     */
    public function getSectionServer(): string;

    /**
     * Returns the template used for translating placeholders tags.
     *
     * ```php
     * return [
     *     'self::::SECTION_TITLE' => $this->getSectionTitle(),
     * ];
     * ```
     */
    public function getTemplate(): array;

    /**
     * Returns the document formatter.
     */
    public function getFormatter(): ThrowableHandlerFormatterInterface;
}
