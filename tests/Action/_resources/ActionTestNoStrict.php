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
use Chevere\Action\Attributes\Strict;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\arrayp;

#[Strict(false)]
final class ActionTestNoStrict extends Action
{
    public static function acceptResponse(): ParameterInterface
    {
        return arrayp();
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
