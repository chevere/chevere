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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Action\Action;
use Chevere\Parameter\Attributes\ParameterAttribute;

final class ActionTestParameterAttributes extends Action
{
    public function run(
        #[ParameterAttribute(description: 'An int')]
        int $int,
        #[ParameterAttribute(description: 'The name', regex: '/^[a-z]$/')]
        string $name,
    ): array {
        return [];
    }
}
