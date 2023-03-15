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
use function Chevere\Parameter\arrayParameter;
use function Chevere\Parameter\integerParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;

final class ActionTestRunReturnExtraArguments extends Action
{
    public function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayParameter(
            name: integerParameter()
        );
    }

    public function run(): array
    {
        return [
            'id' => 1,
            'name' => 'name',
            'extra' => 'extra',
        ];
    }
}
