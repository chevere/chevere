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

namespace Chevere\Components\ThrowableHandler\Documents;

use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerConsoleFormatter;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerFormatterInterface;
use Colors\Color;

final class ThrowableHandlerConsoleDocument extends ThrowableHandlerAbstractDocument
{
    public function getFormatter(): ThrowableHandlerFormatterInterface
    {
        return new ThrowableHandlerConsoleFormatter();
    }

    public function getSectionTitle(): string
    {
        return strtr('%t in %f', [
            '%t' => (string) (new Color(self::TAG_TITLE))->bold()->red(),
            '%f' => $this->formatter->wrapLink(self::TAG_FILE_LINE),
        ]);
    }
}
