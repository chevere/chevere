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
use Chevere\Parameter\Interfaces\ArrayTypeInterface;
use function Chevere\Parameter\stringParameter;

final class ActionTestGenericResponse extends Action
{
    public function getResponseParameter(): ArrayTypeInterface
    {
        return genericParameter(
            V: integerParameter(),
            K: stringParameter()
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
