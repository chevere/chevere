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

use Chevere\ThrowableHandler\Formats\ThrowableHandlerConsoleFormat;
use Chevere\ThrowableHandler\Interfaces\ThrowableHandlerFormatInterface;
use Colors\Color;

final class ThrowableHandlerConsoleDocument extends ThrowableHandlerDocument
{
    public function getFormat(): ThrowableHandlerFormatInterface
    {
        return new ThrowableHandlerConsoleFormat();
    }

    public function getSectionTitle(): string
    {
        return strtr('%t in %f', [
            '%t' => strval((new Color(self::TAG_TITLE))->bold()->red()),
            '%f' => $this->format->wrapLink(self::TAG_FILE_LINE),
        ]);
    }
}
