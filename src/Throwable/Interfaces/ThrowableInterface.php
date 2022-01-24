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

namespace Chevere\Throwable\Interfaces;

use Chevere\Message\Interfaces\MessageInterface;
use Throwable;

/**
 * Describes the component in charge of defining a Chevere throwable.
 */
interface ThrowableInterface extends Throwable
{
    public function message(): MessageInterface;
}
