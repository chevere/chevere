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

namespace Chevere\Components\Hooks\Tests\Interfaces;

use Chevere\Components\Hooks\Interfaces\HookInterface;
use Chevere\Components\Hooks\Tests\MyHookable;

interface MyHookableHookInterface extends HookInterface
{
    public function __invoke(MyHookable $hookable);
}
