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
use Chevere\Attributes\Description;
use Chevere\Attributes\Regex;

final class ActionTestParameterAttributes extends Action
{
    protected function run(
        #[Description('An int')]
        int $int,
        #[Description('The name')]
        #[Regex('/^[a-z]$/')]
        string $name,
    ): array {
        return [];
    }
}
