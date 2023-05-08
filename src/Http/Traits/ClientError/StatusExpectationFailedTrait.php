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

namespace Chevere\Http\Traits\ClientError;

/**
 * @codeCoverageIgnore
 */
trait StatusExpectationFailedTrait
{
    public static function statusError(): int
    {
        return 417;
    }
}
