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

namespace Chevere\Message;

use Chevere\Message\Interfaces\MessageInterface;

function message(string $template): MessageInterface
{
    return new Message($template);
}
