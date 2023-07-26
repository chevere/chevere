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
use function Chevere\Parameter\generic;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;

final class ActionTestGenericResponseError extends Action
{
    public static function acceptResponse(): ParameterInterface
    {
        return generic(
            V: integer(),
            K: string()
        );
    }

    protected function run(): array
    {
        return [
            'a' => 123,
            'b' => '124',
            'c' => 125,
            // ...
        ];
    }
}
