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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\BooleanParameterInterface;

function boolean(
    string $description = '',
    ?bool $default = null,
): BooleanParameterInterface {
    $parameter = new BooleanParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }

    return $parameter;
}
