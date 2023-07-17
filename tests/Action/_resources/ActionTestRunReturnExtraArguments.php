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
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\string;

final class ActionTestRunReturnExtraArguments extends Action
{
    public static function acceptResponse(): ParameterInterface
    {
        return arrayp(
            name: string()
        );
    }

    protected function run(): array
    {
        return [
            'id' => 1,
            'name' => 'name',
            'extra' => 'extra',
        ];
    }
}
