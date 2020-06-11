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

namespace Chevere\Examples;

use Chevere\Interfaces\Plugs\Hooks\HookInterface;

final class HelloWorldHookHook implements HookInterface
{
    public function anchor(): string
    {
        return 'beforeResponse';
    }

    public function at(): string
    {
        return HookHelloWorldController::class;
    }

    public function priority(): int
    {
        return 0;
    }

    public function __invoke(&$argument): void
    {
        $argument .= '!!';
    }
}
