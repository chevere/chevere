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

use Chevere\Components\ExceptionHandler\Formatters\ConsoleFormatter;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

final class ConsoleDocument extends AbstractDocument
{
    /**
     * {@inheritdoc}
     */
    public function getFormatter(): FormatterInterface
    {
        return new ConsoleFormatter;
    }

    public function getTemplate(): array
    {
        $consoleColor = new ConsoleColor;
        $title = $consoleColor->apply(['red', 'bold'], static::TAG_TITLE . ' in ');
        $title .= $this->colorApplyLink(static::TAG_FILE_LINE);

        return [
            static::SECTION_TITLE => $title,
            static::SECTION_MESSAGE => $this->colorApplySection('# Message ' . static::TAG_CODE_WRAP) . "\n" . static::TAG_MESSAGE,
            static::SECTION_ID => $this->colorApplySection('# Incident ID:' . static::TAG_ID) . "\n" . 'Logged at ' . $this->colorApplyLink(static::TAG_LOG_DESTINATION),
            static::SECTION_TIME => $this->colorApplySection('# Time') . "\n" . static::TAG_DATE_TIME_UTC_ATOM . ' [' . static::TAG_TIMESTAMP . ']',
            static::SECTION_STACK => $this->colorApplySection('# Stack trace') . "\n" . static::TAG_STACK,
            static::SECTION_CLIENT => $this->colorApplySection('# Client') . "\n" . static::TAG_CLIENT_IP . ' ' . static::TAG_CLIENT_USER_AGENT,
            static::SECTION_REQUEST => $this->colorApplySection('# Request') . "\n" . static::TAG_SERVER_PROTOCOL . ' ' . static::TAG_REQUEST_METHOD . ' ' . static::TAG_URI,
            static::SECTION_SERVER => $this->colorApplySection('# Server') . "\n" . static::TAG_PHP_UNAME . ' ' . static::TAG_SERVER_SOFTWARE,
        ];
    }

    private function colorApplyLink(string $value): string
    {
        return (new ConsoleColor)->apply(['underline', 'blue'], $value);
    }

    private function colorApplySection(string $value): string
    {
        return (new ConsoleColor)->apply('green', $value);
    }
}
