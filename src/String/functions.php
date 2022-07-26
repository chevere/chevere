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

namespace Chevere\String;

/**
 * @param int<1, max> $length
 */
function randomString(int $length): string
{
    $randomBytes = random_bytes($length);

    return substr(bin2hex($randomBytes), 0, $length);
}
