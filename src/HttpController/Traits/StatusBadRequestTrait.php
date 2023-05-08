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

namespace Chevere\HttpController\Traits;

/**
 * 400
 */
trait StatusBadRequestTrait
{
    public static function statusError(): int
    {
        return 400;
    }
}