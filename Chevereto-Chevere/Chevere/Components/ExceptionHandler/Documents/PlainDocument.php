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
            static::SECTION_TITLE => '%title% in %fileLine%',
            static::SECTION_MESSAGE => '# Message' . "\n" . '%message% %codeWrap%',
            static::SECTION_TIME => '# Time' . "\n" . '%dateTimeUtcAtom% [%timestamp%]',
            static::SECTION_ID => '# Incident ID:%id%' . "\n" . 'Logged at %logFilename%',
            static::SECTION_STACK => '# Stack trace' . "\n" . '%stack%',
            static::SECTION_CLIENT => '# Client' . "\n" . '%clientIp% %clientUserAgent%',
            static::SECTION_REQUEST => '# Request' . "\n" . '%serverProtocol% %requestMethod% %uri%',
            static::SECTION_SERVER => '# Server' . "\n" . '%phpUname% %serverSoftware%',
        ];
    }
}
