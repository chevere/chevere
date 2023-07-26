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

namespace Chevere\Tests\Action\src;

use Chevere\Action\Action;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;
use function Chevere\Parameter\union;

final class ActionTestUnionReturnMissingType extends Action
{
    public static function acceptResponse(): ParameterInterface
    {
        return union(
            string(),
            integer(),
        );
    }

    protected function run(): float
    {
        return 3.1;
    }
}
