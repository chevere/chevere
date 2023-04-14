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
use function Chevere\Parameter\integer;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;

final class ActionTestContainer extends Action
{
    public function acceptContainer(): ParametersInterface
    {
        return parameters(
            id: integer(),
            name: string()
        );
    }

    public function run(): array
    {
        return [];
    }
}
