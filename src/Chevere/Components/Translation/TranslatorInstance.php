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

namespace Chevere\Components\Translation;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Gettext\Translator;

final class TranslatorInstance
{
    private static ?Translator $instance;

    public function __construct(Translator $translator)
    {
        self::$instance = $translator;
    }

    public static function get(): Translator
    {
        if (! isset(self::$instance)) {
            throw new LogicException(
                new Message('No Translator instance present')
            );
        }

        return self::$instance;
    }
}
