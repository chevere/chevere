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

use Chevere\Components\ExceptionHandler\Formatters\PlainFormatter;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;

final class PlainDocument extends AbstractDocument
{
    /**
     * {@inheritdoc}
     */
    public function getFormatter(): FormatterInterface
    {
        return new PlainFormatter;
    }

    public function getSectionsTemplate(): array
    {
        return [
            static::SECTION_TITLE => static::TAG_TITLE . ' in ' . static::TAG_FILE_LINE,
            static::SECTION_MESSAGE => '# Message ' . static::TAG_CODE_WRAP . "\n" . '%message%',
            static::SECTION_TIME => '# Time' . "\n" . static::TAG_DATE_TIME_UTC_ATOM . ' [' . static::TAG_TIMESTAMP . ']',
            static::SECTION_ID => '# Incident ID:' . static::TAG_ID . "\n" . 'Logged at ' . static::TAG_LOG_FILENAME,
            static::SECTION_STACK => '# Stack trace' . "\n" . static::TAG_STACK,
            static::SECTION_CLIENT => '# Client' . "\n" . static::TAG_CLIENT_IP . ' ' . static::TAG_CLIENT_USER_AGENT,
            static::SECTION_REQUEST => '# Request' . "\n" . static::TAG_SERVER_PROTOCOL . ' ' . static::TAG_REQUEST_METHOD . ' ' . static::TAG_URI,
            static::SECTION_SERVER => '# Server' . "\n" . static::TAG_PHP_UNAME . ' ' . static::TAG_SERVER_SOFTWARE,
        ];
    }
}
