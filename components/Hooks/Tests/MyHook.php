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

namespace Chevere\Components\Hooks\Tests;

use Chevere\Components\Hooks\Tests\Interfaces\MyHookableHookInterface;

class MyHook implements MyHookableHookInterface
{
    public function __invoke(MyHookable $hookable)
    {
        $string = $hookable->string();
        $hookable->setString("(hooked $string)");
    }

    public static function anchor(): string
    {
        return MyHookable::HOOK_SET_STRING;
    }

    public static function hookableClassName(): string
    {
        return MyHookable::class;
    }

    public static function priority(): int
    {
        return 0;
    }
}
