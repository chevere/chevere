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
use function Chevere\Parameter\integerParameter;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use function Chevere\Parameter\stringParameter;

final class ActionTestContainer extends Action
{
    public function getContainerParameters(): ParametersInterface
    {
        return new Parameters(
            id: integerParameter(),
            name: stringParameter()
        );
    }

    public function run(): array
    {
        return [];
    }
}
