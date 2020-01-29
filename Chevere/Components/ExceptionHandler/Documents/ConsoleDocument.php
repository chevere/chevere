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
    public function getFormatter(): FormatterInterface
    {
        return new ConsoleFormatter;
    }

    public function getSectionTitle(): string
    {
        return (new ConsoleColor)->apply(['red', 'bold'], self::TAG_TITLE . ' in ')
            . $this->formatter->wrapLink(self::TAG_FILE_LINE);
    }
}
