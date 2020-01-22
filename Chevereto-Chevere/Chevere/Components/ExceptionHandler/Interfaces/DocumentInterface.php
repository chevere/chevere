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

namespace Chevere\Components\ExceptionHandler\Interfaces;

interface DocumentInterface
{
    const SECTION_TITLE = 'title';
    const SECTION_MESSAGE = 'message';
    const SECTION_ID = 'id';
    const SECTION_TIME = 'time';
    const SECTION_STACK = 'stack';
    const SECTION_CLIENT = 'client';
    const SECTION_REQUEST = 'request';
    const SECTION_SERVER = 'server';

    const TAG_TITLE = '%title%';
    const TAG_MESSAGE = '%message%';
    const TAG_CODE_WRAP = '%codeWrap%';
    const TAG_ID = '%id%';
    const TAG_FILE_LINE = '%fileLine%';
    const TAG_DATE_TIME_UTC_ATOM = '%dateTimeUtcAtom%';
    const TAG_TIMESTAMP = '%timestamp%';
    const TAG_LOG_FILENAME = '%logFilename%';
    const TAG_STACK = '%stack%';
    const TAG_CLIENT_IP = '%clientIp%';
    const TAG_CLIENT_USER_AGENT = '%clientUserAgent%';
    const TAG_SERVER_PROTOCOL = '%serverProtocol%';
    const TAG_REQUEST_METHOD = '%requestMethod%';
    const TAG_URI = '%uri%';
    const TAG_PHP_UNAME = '%phpUname%';
    const TAG_SERVER_HOST = '%serverHost%';
    const TAG_SERVER_SOFTWARE = '%serverSoftware%';

    const SECTIONS = [
        0 => self::SECTION_TITLE,
        1 => self::SECTION_MESSAGE,
        2 => self::SECTION_ID,
        3 => self::SECTION_TIME,
        4 => self::SECTION_STACK,
        5 => self::SECTION_CLIENT,
        6 => self::SECTION_REQUEST,
        7 => self::SECTION_SERVER,
    ];

    public function __construct(ExceptionHandlerInterface $exceptionHandler);
}
