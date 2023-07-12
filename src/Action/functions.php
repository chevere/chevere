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

namespace Chevere\Action;

use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\methodParameters;

function getParameters(string $action): ParametersInterface
{
    return methodParameters($action, 'run');
}
