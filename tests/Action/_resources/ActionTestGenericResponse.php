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

namespace Chevere\Tests\Action\_resources;

use Chevere\Action\Action;
use function Chevere\Parameter\generic;
use function Chevere\Parameter\integer;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use function Chevere\Parameter\string;

final class ActionTestGenericResponse extends Action
{
    public function acceptResponse(): ArrayTypeParameterInterface
    {
        return generic(
            V: integer(),
            K: string()
        );
    }

    public function run(): array
    {
        return [
            'id' => 123,
            'id' => 124,
            'id' => 125,
            // ...
        ];
    }
}
