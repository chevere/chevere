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
use function Chevere\Parameter\genericParameter;
use function Chevere\Parameter\integerParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use function Chevere\Parameter\stringParameter;

final class ActionTestGenericResponseError extends Action
{
    public function getResponseParameter(): ArrayTypeParameterInterface
    {
        return genericParameter(
            V: integerParameter(),
            K: stringParameter()
        );
    }

    public function run(): array
    {
        return [
            'a' => 123,
            'b' => '124',
            'c' => 125,
            // ...
        ];
    }
}
